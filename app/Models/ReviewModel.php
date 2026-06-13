<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel reviews
 */
class ReviewModel extends Model
{
    protected $table            = 'reviews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['user_id', 'product_id', 'order_id', 'rating', 'comment', 'photo'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    /**
     * Ambil review produk dengan data user
     */
    public function getByProduct(int $productId, int $limit = 10, int $offset = 0)
    {
        $builder = $this->select('reviews.*, users.name as user_name, users.photo as user_photo')
                        ->join('users', 'users.id = reviews.user_id')
                        ->where('reviews.product_id', $productId);

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $reviews = $builder->orderBy('reviews.created_at', 'DESC')
                           ->findAll($limit, $offset);

        return ['reviews' => $reviews, 'total' => $total];
    }

    /**
     * Cek user sudah review produk di order tertentu
     */
    public function hasReviewed(int $userId, int $productId, int $orderId)
    {
        return (bool) $this->where('user_id', $userId)
                           ->where('product_id', $productId)
                           ->where('order_id', $orderId)
                           ->countAllResults();
    }

    /**
     * Rating rata-rata produk
     */
    public function getAverageRating(int $productId)
    {
        $result = $this->select('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
                       ->where('product_id', $productId)
                       ->first();
        return [
            'avg_rating'    => round((float) ($result['avg_rating'] ?? 0), 1),
            'total_reviews' => (int) ($result['total_reviews'] ?? 0)
        ];
    }
}
