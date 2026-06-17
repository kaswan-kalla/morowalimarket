<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table            = 'stock_movements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['product_id', 'qty', 'type', 'reference_no', 'notes'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    public function getByProduct(int $productId, int $limit = 20): array
    {
        return $this->where('product_id', $productId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }
}
