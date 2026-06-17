<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderFulfillmentModel extends Model
{
    protected $table            = 'order_fulfillments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'order_id',
        'status',
        'notes',
        'picked_by',
        'picked_at',
        'packed_by',
        'packed_at'
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getByOrder(int $orderId)
    {
        return $this->where('order_id', $orderId)->first();
    }
}
