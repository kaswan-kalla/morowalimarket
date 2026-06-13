<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\PaymentModel;

/**
 * Controller Pembayaran + Upload Bukti Transfer
 */
class Payment extends BaseController
{
    protected $orderModel, $paymentModel;

    public function __construct()
    {
        $this->orderModel   = new OrderModel();
        $this->paymentModel = new PaymentModel();
    }

    /**
     * Halaman pembayaran untuk order tertentu
     */
    public function index($orderId)
    {
        $order = $this->orderModel->find($orderId);
        if (!$order || $order['user_id'] != $this->session->get('user_id')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $payment = $this->paymentModel->getByOrder($orderId);

        $data = [
            'meta_title' => 'Pembayaran - ' . $order['order_number'],
            'order'      => $order,
            'payment'    => $payment,
        ];

        return view('payment/index', $data);
    }

    /**
     * Upload bukti pembayaran (AJAX)
     */
    public function upload($orderId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $order = $this->orderModel->find($orderId);
        if (!$order || $order['user_id'] != $this->session->get('user_id')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pesanan tidak ditemukan']);
        }

        $file = $this->request->getFile('proof_image');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Bukti pembayaran wajib diupload']);
        }

        // Validasi file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes) || $file->getSize('kb') > 2048) {
            return $this->response->setJSON(['status' => false, 'message' => 'File harus JPG/PNG/WEBP, max 2MB']);
        }

        $imagePath = upload_image($file, 'uploads/payments');

        $method = $this->request->getPost('payment_method');

        // Hapus payment lama jika ada
        $existingPayment = $this->paymentModel->getByOrder($orderId);
        if ($existingPayment) {
            if ($existingPayment['proof_image']) {
                delete_image($existingPayment['proof_image']);
            }
            $this->paymentModel->delete($existingPayment['id']);
        }

        $this->paymentModel->insert([
            'order_id'    => $orderId,
            'user_id'     => $this->session->get('user_id'),
            'payment_method' => $method ?: 'transfer',
            'amount'      => $order['total_amount'],
            'proof_image' => $imagePath,
            'status'      => 'pending',
        ]);

        // Update status order
        $this->orderModel->update($orderId, ['status' => 'awaiting_payment']);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.',
        ]);
    }
}
