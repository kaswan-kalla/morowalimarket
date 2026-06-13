<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;

class Product extends BaseController
{
    protected $productModel;
    public function __construct() { $this->productModel = new ProductModel(); }

    public function index() { return view('admin/product/index', ['meta_title' => 'Manajemen Produk']); }

    public function data()
    {
        $search = $this->request->getGet('search')['value'] ?? '';
        $start  = (int) $this->request->getGet('start');
        $limit  = (int) $this->request->getGet('length');

        $builder = $this->productModel->select('products.*, stores.name as store_name, categories.name as category_name')
                    ->join('stores', 'stores.id = products.store_id')
                    ->join('categories', 'categories.id = products.category_id', 'left');
        if ($search) {
            $builder->groupStart()->like('products.name', $search)->groupEnd();
        }

        $total = (clone $builder)->countAllResults(false);
        $products = $builder->orderBy('products.created_at', 'DESC')->get($limit, $start)->getResultArray();

        return $this->response->setJSON([
            'draw' => (int) $this->request->getGet('draw'),
            'recordsTotal' => $total, 'recordsFiltered' => $total, 'data' => $products,
        ]);
    }

    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);
        $product = $this->productModel->find($id);
        if (!$product) return $this->response->setJSON(['status' => false]);

        $this->productModel->update($id, ['is_active' => !$product['is_active']]);
        return $this->response->setJSON(['status' => true, 'message' => 'Status produk diperbarui']);
    }
}
