<?php

namespace App\Controllers\Seller;

use App\Controllers\BaseController;
use App\Models\StoreModel;
use App\Models\ProductModel;
use App\Models\OrderModel;

/**
 * Dashboard Seller - statistik & grafik penjualan
 */
class Dashboard extends BaseController
{
    public function index()
    {
        $userId = $this->session->get('user_id');
        $storeModel  = new StoreModel();
        $store = $storeModel->findByUserId($userId);

        if (!$store) {
            return redirect()->to('/seller/toko')->with('info', 'Silakan buat toko terlebih dahulu');
        }

        $productModel = new ProductModel();
        $orderModel   = new OrderModel();

        $data = [
            'meta_title'     => 'Dashboard Seller',
            'store'          => $store,
            'total_products' => $productModel->where('store_id', $store['id'])->countAllResults(),
            'total_orders'   => $orderModel->select('COUNT(DISTINCT orders.id) as cnt')
                                ->join('order_items', 'order_items.order_id = orders.id')
                                ->where('order_items.store_id', $store['id'])->first()['cnt'] ?? 0,
            'revenue_month'  => $orderModel->getStoreRevenue($store['id'], 'month'),
            'revenue_year'   => $orderModel->getStoreRevenue($store['id'], 'year'),
            'latest_products'=> $productModel->where('store_id', $store['id'])->orderBy('created_at', 'DESC')->findAll(5),
            'latest_orders'  => $orderModel->select('orders.*')->join('order_items', 'order_items.order_id = orders.id')
                                ->where('order_items.store_id', $store['id'])->groupBy('orders.id')
                                ->orderBy('orders.created_at', 'DESC')->findAll(5),
        ];

        return view('seller/dashboard', $data);
    }

    /**
     * Data grafik penjualan (AJAX)
     */
    public function salesChart()
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $storeModel = new StoreModel();
        $store = $storeModel->findByUserId($this->session->get('user_id'));
        if (!$store) return $this->response->setJSON(['status' => false]);

        $orderModel = new OrderModel();
        $chart = $orderModel->getSalesChart($store['id'], 30);

        return $this->response->setJSON(['status' => true, 'data' => $chart]);
    }
}
