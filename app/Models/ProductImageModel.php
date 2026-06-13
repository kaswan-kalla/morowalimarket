<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel product_images
 */
class ProductImageModel extends Model
{
    protected $table            = 'product_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['product_id', 'image', 'is_main', 'sort_order'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    /**
     * Ambil semua gambar produk tertentu
     */
    public function getByProduct(int $productId)
    {
        return $this->where('product_id', $productId)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Set gambar sebagai utama (reset yang lain)
     */
    public function setMain(int $imageId, int $productId)
    {
        $this->db->transStart();

        // Reset semua gambar bukan main
        $this->where('product_id', $productId)->set(['is_main' => 0])->update();

        // Set gambar yang dipilih sebagai main
        $this->update($imageId, ['is_main' => 1]);

        // Ambil path gambar untuk update main_image di products
        $image = $this->find($imageId);
        if ($image) {
            $this->db->table('products')
                     ->where('id', $productId)
                     ->update(['main_image' => $image['image']]);
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Hitung jumlah gambar produk
     */
    public function countByProduct(int $productId)
    {
        return $this->where('product_id', $productId)->countAllResults();
    }
}
