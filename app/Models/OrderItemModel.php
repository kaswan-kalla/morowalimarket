<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel order_items
 */
class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'order_id', 'product_id', 'store_id', 'product_name',
        'product_slug', 'product_image', 'price', 'qty', 'subtotal'
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Ambil item dari order tertentu
     */
    public function getByOrder(int $orderId)
    {
        return $this->where('order_id', $orderId)->findAll();
    }

    /**
     * Ambil item per toko dari order tertentu
     */
    public function getByOrderAndStore(int $orderId, int $storeId)
    {
        return $this->where('order_id', $orderId)
                    ->where('store_id', $storeId)
                    ->findAll();
    }
}
