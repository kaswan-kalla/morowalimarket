<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;

/**
 * Laporan Penjualan & Transaksi (Admin)
 */
class Report extends BaseController
{
    public function sales()
    {
        $orderModel = new OrderModel();
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?? date('Y-m-d');

        $orders = $orderModel->select('orders.*, users.name as user_name')
            ->join('users', 'users.id = orders.user_id')
            ->where('orders.status', 'completed')
            ->where('orders.completed_at >=', $startDate)
            ->where('orders.completed_at <=', $endDate . ' 23:59:59')
            ->orderBy('orders.completed_at', 'DESC')
            ->findAll(100);

        $totalRevenue = array_sum(array_column($orders, 'total_amount'));

        return view('admin/report/sales', [
            'meta_title'   => 'Laporan Penjualan',
            'orders'       => $orders,
            'total_revenue'=> $totalRevenue,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
        ]);
    }

    public function transactions()
    {
        $paymentModel = new \App\Models\PaymentModel();
        $payments = $paymentModel->select('payments.*, orders.order_number, users.name as user_name')
            ->join('orders', 'orders.id = payments.order_id')
            ->join('users', 'users.id = payments.user_id')
            ->orderBy('payments.created_at', 'DESC')
            ->findAll(100);

        return view('admin/report/transactions', [
            'meta_title' => 'Laporan Transaksi',
            'payments'   => $payments,
        ]);
    }
}
