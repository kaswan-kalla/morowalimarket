<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel orders
 */
class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'order_number', 'user_id', 'store_id', 'address_id',
        'recipient_name', 'phone', 'shipping_address',
        'courier', 'tracking_number',
        'subtotal', 'shipping_cost', 'discount_amount', 'voucher_code',
        'total_amount', 'payment_method',
        'notes', 'status', 'paid_at', 'shipped_at', 'completed_at',
        'cancelled_at', 'cancel_reason'
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    /**
     * Generate nomor order unik: ORD-YYYYMMDD-XXXX
     */
    public function generateOrderNumber()
    {
        $date = date('Ymd');
        $last = $this->where('order_number LIKE', "ORD-{$date}-%")
                     ->orderBy('id', 'DESC')
                     ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last['order_number']);
            $seq = ((int) end($parts)) + 1;
        }

        return sprintf('ORD-%s-%04d', $date, $seq);
    }

    /**
     * Ambil order dengan detail alamat
     */
    public function getWithAddress(int $orderId)
    {
        return $this->select('orders.*, addresses.recipient_name, addresses.phone as address_phone, addresses.address, addresses.city, addresses.province, addresses.postal_code')
                    ->join('addresses', 'addresses.id = orders.address_id', 'left')
                    ->find($orderId);
    }

    /**
     * Order milik user tertentu
     */
    public function getByUser(int $userId, string $status = null, int $limit = 10, int $offset = 0)
    {
        $builder = $this->where('user_id', $userId);
        if ($status) {
            $builder->where('status', $status);
        }
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $orders = $builder->orderBy('created_at', 'DESC')
                          ->findAll($limit, $offset);

        return ['orders' => $orders, 'total' => $total];
    }

    /**
     * Order per toko (untuk seller dashboard)
     */
    public function getByStore(int $storeId, string $status = null, int $limit = 10, int $offset = 0)
    {
        $builder = $this->select('orders.*')
                        ->join('order_items', 'order_items.order_id = orders.id')
                        ->where('order_items.store_id', $storeId)
                        ->groupBy('orders.id');
        if ($status) {
            $builder->where('orders.status', $status);
        }
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $orders = $builder->orderBy('orders.created_at', 'DESC')
                          ->findAll($limit, $offset);

        return ['orders' => $orders, 'total' => $total];
    }

    /**
     * Hitung omzet toko
     */
    public function getStoreRevenue(int $storeId, string $period = 'month')
    {
        $builder = $this->select('SUM(order_items.subtotal) as revenue')
                        ->join('order_items', 'order_items.order_id = orders.id')
                        ->where('order_items.store_id', $storeId)
                        ->where('orders.status', 'completed');

        if ($period === 'month') {
            $builder->where('orders.completed_at >=', date('Y-m-01'));
        } elseif ($period === 'year') {
            $builder->where('orders.completed_at >=', date('Y-01-01'));
        }

        $result = $builder->first();
        return (float) ($result['revenue'] ?? 0);
    }

    /**
     * Data grafik penjualan harian (30 hari terakhir)
     */
    public function getSalesChart(int $storeId, int $days = 30)
    {
        return $this->select('DATE(orders.completed_at) as date, SUM(order_items.subtotal) as total')
                    ->join('order_items', 'order_items.order_id = orders.id')
                    ->where('order_items.store_id', $storeId)
                    ->where('orders.status', 'completed')
                    ->where('orders.completed_at >=', date('Y-m-d', strtotime("-{$days} days")))
                    ->groupBy('DATE(orders.completed_at)')
                    ->orderBy('date', 'ASC')
                    ->findAll();
    }
}
