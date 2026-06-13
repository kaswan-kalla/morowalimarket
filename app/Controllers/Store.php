<?php

namespace App\Controllers;

use App\Models\StoreModel;
use App\Models\ProductModel;

/**
 * Controller Store (halaman publik toko)
 */
class Store extends BaseController
{
    protected $storeModel;
    protected $productModel;

    public function __construct()
    {
        $this->storeModel   = new StoreModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Halaman profil toko (public)
     */
    public function publicProfile($slug)
    {
        $store = $this->storeModel->findBySlug($slug);
        if (!$store) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $products = $this->productModel->getWithRelations()
                    ->where('products.store_id', $store['id'])
                    ->where('products.is_active', 1)
                    ->orderBy('products.created_at', 'DESC')
                    ->findAll(20);

        $data = [
            'meta_title' => $store['name'],
            'meta_description' => substr($store['description'] ?? '', 0, 160),
            'store'    => $store,
            'products' => $products,
        ];

        return view('store/index', $data);
    }
}
