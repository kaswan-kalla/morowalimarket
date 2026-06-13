<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel carts
 */
class CartModel extends Model
{
    protected $table            = 'carts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['user_id'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Ambil atau buat cart untuk user
     */
    public function getOrCreate(int $userId)
    {
        $cart = $this->where('user_id', $userId)->first();
        if (!$cart) {
            $cartId = $this->insert(['user_id' => $userId]);
            $cart = $this->find($cartId);
        }
        return $cart;
    }
}
