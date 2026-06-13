<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel stores
 */
class StoreModel extends Model
{
    protected $table            = 'stores';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'user_id', 'name', 'slug', 'logo', 'banner', 'description',
        'address', 'city', 'province', 'postal_code', 'phone',
        'is_open', 'rating', 'total_sales'
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules  = [
        'name'    => 'required|min_length[3]|max_length[150]',
        'slug'    => 'required|max_length[160]',
        'phone'   => 'permit_empty|max_length[20]',
    ];

    /**
     * Cari toko berdasarkan slug
     */
    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Cari toko berdasarkan user_id
     */
    public function findByUserId(int $userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Ambil toko beserta data owner
     */
    public function getWithOwner(int $storeId)
    {
        return $this->select('stores.*, users.name as owner_name, users.email as owner_email')
                    ->join('users', 'users.id = stores.user_id')
                    ->find($storeId);
    }

    /**
     * Update rating toko berdasarkan review produk
     */
    public function updateRating(int $storeId)
    {
        $db = \Config\Database::connect();
        $result = $db->query("
            SELECT AVG(r.rating) as avg_rating
            FROM reviews r
            JOIN products p ON p.id = r.product_id
            WHERE p.store_id = ? AND r.deleted_at IS NULL
        ", [$storeId])->getRow();

        $avgRating = $result ? round($result->avg_rating, 2) : 0;
        $this->update($storeId, ['rating' => $avgRating]);
    }
}
