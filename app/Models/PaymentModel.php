<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel payments
 */
class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'order_id', 'user_id', 'payment_method', 'amount', 'proof_image',
        'status', 'verified_by', 'verified_at', 'notes'
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Ambil payment berdasarkan order
     */
    public function getByOrder(int $orderId)
    {
        return $this->where('order_id', $orderId)
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }

    /**
     * Ambil payment yang menunggu verifikasi
     */
    public function getPending(int $limit = 20, int $offset = 0)
    {
        $builder = $this->select('payments.*, orders.order_number, users.name as user_name')
                        ->join('orders', 'orders.id = payments.order_id')
                        ->join('users', 'users.id = payments.user_id')
                        ->where('payments.status', 'pending');

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $payments = $builder->orderBy('payments.created_at', 'ASC')
                            ->findAll($limit, $offset);

        return ['payments' => $payments, 'total' => $total];
    }
}
