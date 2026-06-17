<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel payment_logs (menyimpan seluruh payload webhook Midtrans)
 */
class PaymentLogModel extends Model
{
    protected $table            = 'payment_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'order_id',
        'transaction_id',
        'transaction_status',
        'fraud_status',
        'payment_type',
        'status_code',
        'signature_key',
        'raw_payload',
        'is_processed',
        'error_message'
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    /**
     * Ambil log berdasarkan order
     */
    public function getByOrder(int $orderId): array
    {
        return $this->where('order_id', $orderId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
