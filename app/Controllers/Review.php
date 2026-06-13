<?php

namespace App\Controllers;

use App\Models\ReviewModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\StoreModel;

/**
 * Controller Review & Rating
 */
class Review extends BaseController
{
    protected $reviewModel, $orderModel, $orderItemModel, $storeModel;

    public function __construct()
    {
        $this->reviewModel     = new ReviewModel();
        $this->orderModel      = new OrderModel();
        $this->orderItemModel  = new OrderItemModel();
        $this->storeModel      = new StoreModel();
    }

    /**
     * Submit review (AJAX)
     */
    public function submit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $userId    = $this->session->get('user_id');
        $productId = (int) $this->request->getPost('product_id');
        $orderId   = (int) $this->request->getPost('order_id');
        $rating    = (int) $this->request->getPost('rating');
        $comment   = $this->request->getPost('comment');

        // Validasi
        if ($rating < 1 || $rating > 5) {
            return $this->response->setJSON(['status' => false, 'message' => 'Rating harus 1-5']);
        }

        // Cek order milik user & status completed
        $order = $this->orderModel->find($orderId);
        if (!$order || $order['user_id'] != $userId || $order['status'] !== 'completed') {
            return $this->response->setJSON(['status' => false, 'message' => 'Pesanan tidak valid']);
        }

        // Cek sudah review belum
        if ($this->reviewModel->hasReviewed($userId, $productId, $orderId)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Anda sudah memberikan review']);
        }

        // Upload foto review jika ada
        $photoPath = null;
        $file = $this->request->getFile('photo');
        if ($file && $file->isValid()) {
            $photoPath = upload_image($file, 'uploads/reviews');
        }

        $this->reviewModel->insert([
            'user_id'    => $userId,
            'product_id' => $productId,
            'order_id'   => $orderId,
            'rating'     => $rating,
            'comment'    => $comment,
            'photo'      => $photoPath,
        ]);

        // Update rating toko
        $orderItem = $this->orderItemModel->where('order_id', $orderId)
                                           ->where('product_id', $productId)
                                           ->first();
        if ($orderItem) {
            $this->storeModel->updateRating($orderItem['store_id']);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Review berhasil dikirim'
        ]);
    }

    /**
     * Get reviews by product (AJAX - pagination)
     */
    public function getByProduct($productId)
    {
        $offset = max(0, (int) $this->request->getGet('offset'));
        $result = $this->reviewModel->getByProduct($productId, 10, $offset);
        $avg    = $this->reviewModel->getAverageRating($productId);

        return $this->response->setJSON([
            'status' => true,
            'data'   => array_merge($result, $avg),
        ]);
    }
}
