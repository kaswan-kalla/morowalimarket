<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\CartModel;
use App\Models\CartItemModel;

/**
 * Controller Pesanan (Order)
 */
class Order extends BaseController
{
    protected $orderModel, $orderItemModel, $paymentModel, $productModel;
    protected $cartModel, $cartItemModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->paymentModel   = new PaymentModel();
        $this->productModel   = new ProductModel();
        $this->cartModel      = new CartModel();
        $this->cartItemModel  = new CartItemModel();
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
            'content'    => 'order',
            'meta_title' => 'Pesanan Saya',
            'orders'     => $result['orders'],
            'total'      => $result['total'],
            'page'       => $page,
            'limit'      => $limit,
            'status'     => $status,
        ];

        return view('layout/marketplace_content', $data);
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
            'content'    => 'order',
            'subview'    => 'detail',
            'meta_title' => 'Detail Pesanan ' . $order['order_number'],
            'order'      => $order,
            'items'      => $this->orderItemModel->getByOrder($id),
            'payment'    => $this->paymentModel->getByOrder($id),
        ];

        return view('layout/marketplace_content', $data);
    }

    /**
     * Batalkan pesanan (AJAX)
     */
    public function cancel()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $id = (int) $this->request->getPost('id');
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
    public function complete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $id = (int) $this->request->getPost('id');
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

    /**
     * Pesan ulang produk dari order yang dibatalkan (AJAX)
     */
    public function reorder()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $id = (int) $this->request->getPost('id');
        $userId = $this->session->get('user_id');

        $order = $this->orderModel->find($id);
        if (!$order || $order['user_id'] != $userId || $order['status'] !== 'cancelled') {
            return $this->response->setJSON(['status' => false, 'message' => 'Pesanan tidak dapat dipesan ulang']);
        }

        $items = $this->orderItemModel->getByOrder($id);
        if (empty($items)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada produk di pesanan ini']);
        }

        $cart = $this->cartModel->getOrCreate($userId);
        $added = 0;
        $skipped = [];

        foreach ($items as $item) {
            $product = $this->productModel->find($item['product_id']);
            if (!$product || !$product['is_active']) {
                $skipped[] = $item['product_name'];
                continue;
            }
            if ($product['stock'] < $item['qty']) {
                $skipped[] = $item['product_name'] . ' (stok tidak cukup)';
                continue;
            }

            // Cek apakah sudah ada di cart
            $existing = $this->cartItemModel->where('cart_id', $cart['id'])
                ->where('product_id', $item['product_id'])
                ->first();

            if ($existing) {
                $this->cartItemModel->update($existing['id'], [
                    'qty' => $existing['qty'] + $item['qty'],
                ]);
            } else {
                $this->cartItemModel->insert([
                    'cart_id'    => $cart['id'],
                    'product_id' => $item['product_id'],
                    'store_id'   => $item['store_id'],
                    'qty'        => $item['qty'],
                ]);
            }
            $added++;
        }

        if ($added === 0) {
            $msg = 'Tidak ada produk yang bisa ditambahkan. ' . implode(', ', $skipped);
            return $this->response->setJSON(['status' => false, 'message' => $msg]);
        }

        $msg = $added . ' produk berhasil ditambahkan ke keranjang';
        if (!empty($skipped)) {
            $msg .= '. Dilewati: ' . implode(', ', $skipped);
        }

        return $this->response->setJSON([
            'status'   => true,
            'message'  => $msg,
            'redirect' => base_url('cart'),
        ]);
    }
}
