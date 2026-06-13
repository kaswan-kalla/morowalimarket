<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;

/**
 * Controller Pesanan (Order)
 */
class Order extends BaseController
{
    protected $orderModel, $orderItemModel, $paymentModel, $productModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->paymentModel   = new PaymentModel();
        $this->productModel   = new ProductModel();
    }

    /**
     * Daftar pesanan user
     */
    public function index()
    {
        $userId = $this->session->get('user_id');
        $status = $this->request->getGet('status');
        $page   = max(1, (int) $this->request->getGet('page'));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $result = $this->orderModel->getByUser($userId, $status, $limit, $offset);

        // Ambil items untuk setiap order
        foreach ($result['orders'] as &$order) {
            $order['items'] = $this->orderItemModel->getByOrder($order['id']);
            $order['payment'] = $this->paymentModel->getByOrder($order['id']);
        }

        $data = [
            'meta_title' => 'Pesanan Saya',
            'orders'     => $result['orders'],
            'total'      => $result['total'],
            'page'       => $page,
            'limit'      => $limit,
            'status'     => $status,
        ];

        return view('order/index', $data);
    }

    /**
     * Detail pesanan
     */
    public function detail($id)
    {
        $order = $this->orderModel->getWithAddress($id);
        if (!$order || $order['user_id'] != $this->session->get('user_id')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'meta_title' => 'Detail Pesanan ' . $order['order_number'],
            'order'      => $order,
            'items'      => $this->orderItemModel->getByOrder($id),
            'payment'    => $this->paymentModel->getByOrder($id),
        ];

        return view('order/detail', $data);
    }

    /**
     * Batalkan pesanan (AJAX)
     */
    public function cancel($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $order = $this->orderModel->find($id);
        if (!$order || $order['user_id'] != $this->session->get('user_id')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pesanan tidak ditemukan']);
        }

        if (!in_array($order['status'], ['pending', 'awaiting_payment'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pesanan tidak dapat dibatalkan']);
        }

        $this->orderModel->db->transStart();

        // Kembalikan stok
        $items = $this->orderItemModel->getByOrder($id);
        foreach ($items as $item) {
            $this->productModel->increaseStock($item['product_id'], $item['qty']);
        }

        $this->orderModel->update($id, [
            'status'        => 'cancelled',
            'cancelled_at'  => date('Y-m-d H:i:s'),
            'cancel_reason' => $this->request->getPost('reason') ?? 'Dibatalkan oleh pembeli',
        ]);

        $this->orderModel->db->transComplete();

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pesanan berhasil dibatalkan'
        ]);
    }

    /**
     * Konfirmasi pesanan diterima (AJAX)
     */
    public function complete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $order = $this->orderModel->find($id);
        if (!$order || $order['user_id'] != $this->session->get('user_id') || $order['status'] !== 'shipped') {
            return $this->response->setJSON(['status' => false, 'message' => 'Pesanan tidak dapat diselesaikan']);
        }

        $this->orderModel->update($id, [
            'status'       => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pesanan telah diselesaikan'
        ]);
    }
}
