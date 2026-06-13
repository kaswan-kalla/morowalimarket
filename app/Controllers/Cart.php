<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Models\ProductModel;

/**
 * Controller Keranjang Belanja (AJAX)
 */
class Cart extends BaseController
{
    protected $cartModel;
    protected $cartItemModel;
    protected $productModel;

    public function __construct()
    {
        $this->cartModel     = new CartModel();
        $this->cartItemModel = new CartItemModel();
        $this->productModel  = new ProductModel();
    }

    /**
     * Halaman keranjang
     */
    public function index()
    {
        $cart = $this->cartModel->getOrCreate($this->session->get('user_id'));
        $data = [
            'content'    => 'cart',
            'meta_title' => 'Keranjang Belanja',
            'cart_items' => $this->cartItemModel->getCartItems($cart['id']),
            'subtotal'   => $this->cartItemModel->getSubtotal($cart['id']),
        ];

        return view('layout/marketplace_content', $data);
    }

    /**
     * Tambah item ke keranjang (AJAX)
     */
    public function add()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $productId = (int) $this->request->getPost('product_id');
        $qty       = max(1, (int) $this->request->getPost('quantity', 1));

        $product = $this->productModel->find($productId);
        if (!$product || !$product['is_active']) {
            return $this->response->setJSON(['status' => false, 'message' => 'Produk tidak ditemukan']);
        }

        if ($product['stock'] < $qty) {
            return $this->response->setJSON(['status' => false, 'message' => 'Stok tidak mencukupi']);
        }

        $cart = $this->cartModel->getOrCreate($this->session->get('user_id'));

        // Cek apakah produk sudah ada di cart
        $existing = $this->cartItemModel->findItem($cart['id'], $productId);

        if ($existing) {
            $newQty = $existing['qty'] + $qty;
            if ($newQty > $product['stock']) {
                return $this->response->setJSON(['status' => false, 'message' => 'Stok tidak mencukupi']);
            }
            $this->cartItemModel->update($existing['id'], ['qty' => $newQty]);
        } else {
            $this->cartItemModel->insert([
                'cart_id'    => $cart['id'],
                'product_id' => $productId,
                'qty'        => $qty,
            ]);
        }

        $totalItems = $this->cartItemModel->countItems($cart['id']);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Produk ditambahkan ke keranjang',
            'data'    => ['cart_count' => $totalItems]
        ]);
    }

    /**
     * Update quantity item (AJAX)
     */
    public function updateQty()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $itemId = (int) $this->request->getPost('item_id');
        $qty    = max(1, (int) $this->request->getPost('quantity'));

        $item = $this->cartItemModel->find($itemId);
        if (!$item) {
            return $this->response->setJSON(['status' => false, 'message' => 'Item tidak ditemukan']);
        }

        $product = $this->productModel->find($item['product_id']);
        if ($qty > $product['stock']) {
            return $this->response->setJSON(['status' => false, 'message' => 'Stok tidak mencukupi']);
        }

        $this->cartItemModel->update($itemId, ['qty' => $qty]);

        $cart = $this->cartModel->getOrCreate($this->session->get('user_id'));

        return $this->response->setJSON([
            'status'   => true,
            'message'  => 'Keranjang diperbarui',
            'data'     => [
                'subtotal'   => $this->cartItemModel->getSubtotal($cart['id']),
                'cart_count' => $this->cartItemModel->countItems($cart['id']),
            ]
        ]);
    }

    /**
     * Hapus item dari keranjang (AJAX)
     */
    public function remove()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $itemId = (int) $this->request->getPost('item_id');
        $this->cartItemModel->delete($itemId);

        $cart = $this->cartModel->getOrCreate($this->session->get('user_id'));

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Item dihapus dari keranjang',
            'data'    => [
                'subtotal'   => $this->cartItemModel->getSubtotal($cart['id']),
                'cart_count' => $this->cartItemModel->countItems($cart['id']),
            ]
        ]);
    }

    /**
     * Ringkasan cart (AJAX - untuk widget)
     */
    public function summary()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $cart = $this->cartModel->getOrCreate($this->session->get('user_id'));

        return $this->response->setJSON([
            'status' => true,
            'data'   => [
                'items'      => $this->cartItemModel->getCartItems($cart['id']),
                'subtotal'   => $this->cartItemModel->getSubtotal($cart['id']),
                'item_count' => $this->cartItemModel->countItems($cart['id']),
            ]
        ]);
    }
}
