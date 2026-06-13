<?php

namespace App\Controllers;

use App\Models\WishlistModel;

/**
 * Controller Wishlist (AJAX)
 */
class Wishlist extends BaseController
{
    protected $wishlistModel;

    public function __construct()
    {
        $this->wishlistModel = new WishlistModel();
    }

    /**
     * Halaman wishlist
     */
    public function index()
    {
        $userId = $this->session->get('user_id');
        $page   = max(1, (int) $this->request->getGet('page'));
        $limit  = 20;
        $offset = ($page - 1) * $limit;

        $result = $this->wishlistModel->getUserWishlist($userId, $limit, $offset);

        $data = [
            'content'    => 'wishlist',
            'meta_title' => 'Wishlist Saya',
            'items'      => $result['items'],
            'total'      => $result['total'],
            'page'       => $page,
            'limit'      => $limit,
        ];

        return view('layout/marketplace_content', $data);
    }

    /**
     * Toggle wishlist (AJAX)
     */
    public function toggle()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $userId    = $this->session->get('user_id');
        $productId = (int) $this->request->getPost('product_id');

        $added = $this->wishlistModel->toggle($userId, $productId);

        return $this->response->setJSON([
            'status'  => true,
            'message' => $added ? 'Ditambahkan ke wishlist' : 'Dihapus dari wishlist',
            'data'    => ['is_wishlisted' => $added]
        ]);
    }

    /**
     * Get wishlist via AJAX (pagination)
     */
    public function getList()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $userId = $this->session->get('user_id');
        $offset = max(0, (int) $this->request->getPost('offset'));
        $limit  = 20;

        $result = $this->wishlistModel->getUserWishlist($userId, $limit, $offset);

        return $this->response->setJSON([
            'status'  => true,
            'data'    => $result['items'],
            'total'   => $result['total'],
            'hasMore' => ($offset + $limit) < $result['total'],
        ]);
    }
}
