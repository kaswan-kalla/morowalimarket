<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\OrderModel;
use App\Models\StoreModel;

/**
 * Verifikasi Pembayaran (Admin)
 */
class Payment extends BaseController
{
    protected $paymentModel, $orderModel;
    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->orderModel   = new OrderModel();
    }

    public function index() { return view('admin/payment/index', ['meta_title' => 'Verifikasi Pembayaran']); }

    public function data()
    {
        $result = $this->paymentModel->getPending();
        return $this->response->setJSON(['status' => true, 'data' => $result]);
    }

    /** Verifikasi pembayaran */
    public function verify($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $payment = $this->paymentModel->find($id);
        if (!$payment) return $this->response->setJSON(['status' => false, 'message' => 'Payment tidak ditemukan']);

        $this->paymentModel->update($id, [
            'status'      => 'verified',
            'verified_by' => $this->session->get('user_id'),
            'verified_at' => date('Y-m-d H:i:s'),
        ]);

        // Update order status ke processing
        $this->orderModel->update($payment['order_id'], [
            'status'  => 'processing',
            'paid_at' => date('Y-m-d H:i:s'),
        ]);

        // Update total_sales toko
        $storeModel = new StoreModel();
        $orderItems = $this->orderModel->db->table('order_items')
            ->select('store_id, SUM(qty) as total_qty')
            ->where('order_id', $payment['order_id'])
            ->groupBy('store_id')
            ->get()->getResultArray();

        foreach ($orderItems as $item) {
            $storeModel->set('total_sales', "total_sales + {$item['total_qty']}", false)
                       ->where('id', $item['store_id'])->update();
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Pembayaran diverifikasi']);
    }

    /** Tolak pembayaran */
    public function reject($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $this->paymentModel->update($id, [
            'status'           => 'rejected',
            'verified_by'      => $this->session->get('user_id'),
            'verified_at'      => date('Y-m-d H:i:s'),
            'rejection_reason' => $this->request->getPost('reason'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Pembayaran ditolak']);
    }
}
