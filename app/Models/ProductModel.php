<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel products
 */
class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'store_id', 'category_id', 'name', 'slug', 'sku', 'description',
        'price', 'discount_price', 'weight', 'stock', 'sold',
        'is_active', 'main_image'
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules  = [
        'name'      => 'required|min_length[3]|max_length[200]',
        'slug'      => 'required|max_length[220]',
        'price'     => 'required|numeric|greater_than[0]',
        'stock'     => 'required|integer|greater_than_equal_to[0]',
        'weight'    => 'required|numeric|greater_than[0]',
    ];

    /**
     * Cari produk berdasarkan slug
     */
    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Ambil produk dengan data toko dan kategori
     */
    public function getWithRelations(int $productId = null)
    {
        $builder = $this->select('products.*, stores.name as store_name, stores.slug as store_slug, categories.name as category_name')
                        ->join('stores', 'stores.id = products.store_id')
                        ->join('categories', 'categories.id = products.category_id', 'left');

        if ($productId) {
            return $builder->find($productId);
        }
        return $builder;
    }

    /**
     * Pencarian produk dengan filter
     */
    public function searchProducts(array $filters = [], int $limit = 12, int $offset = 0)
    {
        $builder = $this->select('products.*, stores.name as store_name, stores.slug as store_slug, stores.city as store_city, categories.name as category_name')
                        ->join('stores', 'stores.id = products.store_id')
                        ->join('categories', 'categories.id = products.category_id', 'left')
                        ->where('products.is_active', 1);

        // Filter pencarian nama
        if (!empty($filters['q'])) {
            $builder->like('products.name', $filters['q']);
        }

        // Filter kategori
        if (!empty($filters['category_id'])) {
            $builder->where('products.category_id', $filters['category_id']);
        }

        // Filter slug kategori
        if (!empty($filters['category_slug'])) {
            $builder->where('categories.slug', $filters['category_slug']);
        }

        // Filter rentang harga
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $builder->where('products.price >=', $filters['min_price']);
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $builder->where('products.price <=', $filters['max_price']);
        }

        // Filter lokasi (kota)
        if (!empty($filters['city'])) {
            $builder->like('stores.city', $filters['city']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'cheapest':
                $builder->orderBy('products.price', 'ASC');
                break;
            case 'expensive':
                $builder->orderBy('products.price', 'DESC');
                break;
            case 'popular':
                $builder->orderBy('products.sold', 'DESC');
                break;
            default: // newest
                $builder->orderBy('products.created_at', 'DESC');
                break;
        }

        // Hitung total untuk pagination
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        // Ambil data dengan limit
        $products = $builder->get($limit, $offset)->getResultArray();

        return ['products' => $products, 'total' => $total];
    }

    /**
     * Kurangi stok setelah pembelian
     */
    public function decreaseStock(int $productId, int $qty)
    {
        return $this->set('stock', "stock - {$qty}", false)
                    ->set('sold', "sold + {$qty}", false)
                    ->where('id', $productId)
                    ->where('stock >=', $qty)
                    ->update();
    }

    /**
     * Kembalikan stok saat pembatalan
     */
    public function increaseStock(int $productId, int $qty)
    {
        return $this->set('stock', "stock + {$qty}", false)
                    ->set('sold', "sold - {$qty}", false)
                    ->where('id', $productId)
                    ->update();
    }

    /**
     * Produk terlaris per toko
     */
    public function getBestSellers(int $storeId = null, int $limit = 10)
    {
        $builder = $this->where('is_active', 1);
        if ($storeId) {
            $builder->where('store_id', $storeId);
        }
        return $builder->orderBy('sold', 'DESC')
                       ->findAll($limit);
    }
}
