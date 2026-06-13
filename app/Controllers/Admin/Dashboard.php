<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\StoreModel;
use App\Models\ProductModel;
use App\Models\OrderModel;

/**
 * Dashboard Admin - statistik global
 */
class Dashboard extends BaseController
{
    public function index()
    {
        $userModel    = new UserModel();
        $storeModel   = new StoreModel();
        $productModel = new ProductModel();
        $orderModel   = new OrderModel();

        $data = [
            'meta_title'    => 'Admin Dashboard',
            'total_users'   => $userModel->countByRole(),
            'total_sellers' => $userModel->countByRole('seller'),
            'total_stores'  => $storeModel->countAllResults(),
            'total_products' => $productModel->countAllResults(),
            'total_orders'  => $orderModel->countAllResults(),
            'revenue_total' => $orderModel->select('SUM(total_amount) as rev')->where('status', 'completed')->first()['rev'] ?? 0,
        ];

        return view('admin/dashboard', $data);
    }
}
