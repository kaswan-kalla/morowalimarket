<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel vouchers
 */
class VoucherModel extends Model
{
    protected $table            = 'vouchers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'code', 'discount_type', 'discount_value', 'min_purchase', 'max_discount',
        'max_usage', 'used_count', 'start_date', 'expired_at', 'is_active'
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Validasi dan hitung diskon voucher
     */
    public function validateAndCalculate(string $code, float $subtotal)
    {
        $voucher = $this->where('code', $code)
                        ->where('is_active', 1)
                        ->where('start_date <=', date('Y-m-d'))
                        ->where('expired_at >=', date('Y-m-d'))
                        ->first();

        if (!$voucher) {
            return ['valid' => false, 'message' => 'Voucher tidak ditemukan atau sudah kadaluarsa'];
        }

        if ($subtotal < $voucher['min_purchase']) {
            return ['valid' => false, 'message' => 'Minimum order Rp ' . number_format($voucher['min_purchase'], 0, ',', '.')];
        }

        if ($voucher['max_usage'] > 0 && $voucher['used_count'] >= $voucher['max_usage']) {
            return ['valid' => false, 'message' => 'Voucher sudah habis digunakan'];
        }

        // Hitung diskon
        $discount = 0;
        if ($voucher['discount_type'] === 'percentage') {
            $discount = $subtotal * ($voucher['discount_value'] / 100);
            if ($voucher['max_discount'] && $discount > $voucher['max_discount']) {
                $discount = $voucher['max_discount'];
            }
        } else {
            $discount = $voucher['discount_value'];
        }

        return [
            'valid'    => true,
            'discount' => $discount,
            'voucher'  => $voucher
        ];
    }

    /**
     * Tambah jumlah penggunaan voucher
     */
    public function incrementUsage(int $voucherId)
    {
        return $this->set('used_count', 'used_count + 1', false)
                    ->where('id', $voucherId)
                    ->update();
    }
}
