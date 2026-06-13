<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel wishlists
 */
class WishlistModel extends Model
{
    protected $table            = 'wishlists';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['user_id', 'product_id'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Cek apakah produk sudah di-wishlist
     */
    public function isWishlisted(int $userId, int $productId)
    {
        return (bool) $this->where('user_id', $userId)
                           ->where('product_id', $productId)
                           ->countAllResults();
    }

    /**
     * Toggle wishlist: tambah jika belum, hapus jika sudah
     */
    public function toggle(int $userId, int $productId)
    {
        $existing = $this->where('user_id', $userId)
                         ->where('product_id', $productId)
                         ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return false; // dihapus
        }

        $this->insert(['user_id' => $userId, 'product_id' => $productId]);
        return true; // ditambah
    }

    /**
     * Ambil wishlist user dengan data produk
     */
    public function getUserWishlist(int $userId, int $limit = 20, int $offset = 0)
    {
        $builder = $this->select('wishlists.*, products.name, products.slug, products.price, products.discount_price, products.main_image, stores.name as store_name, stores.slug as store_slug')
                        ->join('products', 'products.id = wishlists.product_id')
                        ->join('stores', 'stores.id = products.store_id')
                        ->where('wishlists.user_id', $userId)
                        ->where('products.is_active', 1);

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $items = $builder->orderBy('wishlists.created_at', 'DESC')
                         ->findAll($limit, $offset);

        return ['items' => $items, 'total' => $total];
    }
}
