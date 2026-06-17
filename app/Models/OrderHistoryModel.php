<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderHistoryModel extends Model
{
    protected $table            = 'order_histories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['order_id', 'message', 'created_by'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    public function getByOrder(int $orderId): array
    {
        return $this->where('order_id', $orderId)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }
}
