<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel categories
 */
class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['name', 'slug', 'icon', 'parent_id'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules  = [
        'name' => 'required|min_length[2]|max_length[100]',
        'slug' => 'required|max_length[110]',
    ];

    /**
     * Cari kategori berdasarkan slug
     */
    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Ambil semua kategori parent (tanpa parent)
     */
    public function getParents()
    {
        return $this->where('parent_id', null)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil sub-kategori dari parent tertentu
     */
    public function getChildren(int $parentId)
    {
        return $this->where('parent_id', $parentId)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil kategori dengan jumlah produk
     */
    public function getWithProductCount()
    {
        return $this->select('categories.*, COUNT(products.id) as product_count')
                    ->join('products', 'products.category_id = categories.id', 'left')
                    ->groupBy('categories.id')
                    ->orderBy('categories.name', 'ASC')
                    ->findAll();
    }
}
