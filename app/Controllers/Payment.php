<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\PaymentLogModel;
use App\Services\MidtransService;
use App\Services\PaymentService;

/**
 * Controller Pembayaran + Upload Bukti Transfer + Midtrans Webhook
 */
class Payment extends BaseController
{
    protected $orderModel, $paymentModel, $paymentLogModel;
    protected $midtransService;
    protected $paymentService;

    public function __construct()
    {
        $this->orderModel      = new OrderModel();
        $this->paymentModel    = new PaymentModel();
        $this->paymentLogModel = new PaymentLogModel();
        $this->midtransService = new MidtransService();
        $this->paymentService  = new PaymentService();
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
        $paymentDetails = !empty($order['payment_details']) ? json_decode($order['payment_details'], true) : null;

        $data = [
            'content'        => 'payment',
            'meta_title'     => 'Pembayaran - ' . $order['order_number'],
            'order'          => $order,
            'payment'        => $payment,
            'paymentDetails' => $paymentDetails,
            'snapToken'      => $order['snap_token'] ?? '',
            'snapUrl'        => $this->midtransService->getSnapUrl(),
            'clientKey'      => $this->midtransService->getClientKey(),
        ];

        return view('layout/marketplace_content', $data);
    }

    /**
     * Upload bukti pembayaran manual (AJAX)
     */
    public function upload()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $orderId = (int) $this->request->getPost('order_id');
        $order = $this->orderModel->find($orderId);
        if (!$order || $order['user_id'] != $this->session->get('user_id')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pesanan tidak ditemukan']);
        }

        $file = $this->request->getFile('proof');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Bukti pembayaran wajib diupload']);
        }

        // Validasi file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes) || $file->getSize() > 2 * 1024 * 1024) {
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
            'order_id'       => $orderId,
            'user_id'        => $this->session->get('user_id'),
            'payment_method' => $method ?: 'transfer',
            'amount'         => $order['total_amount'],
            'proof_image'    => $imagePath,
            'notes'          => $this->request->getPost('notes'),
            'status'         => 'pending',
        ]);

        // Update status order
        $this->orderModel->update($orderId, ['status' => 'awaiting_payment']);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.',
        ]);
    }

    /**
     * Endpoint webhook notifikasi dari Midtrans.
     * Route: POST /payment/notification (tanpa auth filter)
     *
     * Seluruh pipeline bisnis dijalankan oleh PaymentService:
     * - Update order status, payment, log
     * - Kurangi stok + catat mutasi
     * - Generate invoice
     * - Notifikasi customer & admin
     * - Audit trail
     * Semua dalam 1 database transaction.
     */
    public function notification()
    {
        $rawPayload = file_get_contents('php://input');

        $result = $this->paymentService->processNotification($rawPayload);

        return $this->response->setStatusCode($result['httpCode'])->setJSON([
            'status'  => $result['status'],
            'message' => $result['message'],
        ]);
    }
}
