<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel cart_items
 */
class CartItemModel extends Model
{
    protected $table            = 'cart_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['cart_id', 'product_id', 'qty', 'notes'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Ambil semua item dalam cart beserta data produk & toko
     */
    public function getCartItems(int $cartId)
    {
        return $this->select('cart_items.id, cart_items.cart_id, cart_items.product_id, cart_items.qty, cart_items.notes, cart_items.created_at, cart_items.updated_at, products.name as product_name, products.slug as product_slug, products.price, products.discount_price, products.stock, products.weight, products.main_image, stores.name as store_name, stores.slug as store_slug, stores.id as store_id')
            ->join('products', 'products.id = cart_items.product_id')
            ->join('stores', 'stores.id = products.store_id')
            ->where('cart_items.cart_id', $cartId)
            ->where('products.is_active', 1)
            ->orderBy('cart_items.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Cari item di cart berdasarkan produk
     */
    public function findItem(int $cartId, int $productId)
    {
        return $this->where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();
    }

    /**
     * Hitung total item di cart
     */
    public function countItems(int $cartId)
    {
        return $this->select('SUM(cart_items.qty) as total_qty')
            ->where('cart_id', $cartId)
            ->first()['total_qty'] ?? 0;
    }

    /**
     * Hitung subtotal cart
     */
    public function getSubtotal(int $cartId)
    {
        $result = $this->select('SUM(
            cart_items.qty * CASE WHEN products.discount_price > 0 THEN products.discount_price ELSE products.price END
        ) as subtotal')
            ->join('products', 'products.id = cart_items.product_id')
            ->where('cart_items.cart_id', $cartId)
            ->first();

        return (float) ($result['subtotal'] ?? 0);
    }

    /**
     * Hapus semua item di cart (setelah checkout)
     */
    public function clearCart(int $cartId, array $productIds = [])
    {
        $builder = $this->where('cart_id', $cartId);
        if (!empty($productIds)) {
            $builder->whereIn('product_id', $productIds);
        }
        return $builder->delete();
    }
}
