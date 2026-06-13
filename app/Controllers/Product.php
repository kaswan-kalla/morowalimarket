<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;
use App\Models\ReviewModel;

/**
 * Controller Produk (Public): listing, detail, search, filter
 */
class Product extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $imageModel;
    protected $reviewModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->imageModel    = new ProductImageModel();
        $this->reviewModel   = new ReviewModel();
    }

    /**
     * Daftar semua produk dengan pagination
     */
    public function index()
    {
        $page  = max(1, (int) $this->request->getGet('page'));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $result = $this->productModel->searchProducts([
            'sort' => $this->request->getGet('sort') ?? 'newest'
        ], $limit, $offset);

        $data = [
            'meta_title'  => 'Semua Produk',
            'products'    => $result['products'],
            'total'       => $result['total'],
            'page'        => $page,
            'limit'       => $limit,
            'categories'  => $this->categoryModel->getParents(),
        ];

        return view('product/index', $data);
    }

    /**
     * Detail produk berdasarkan slug
     */
    public function detail($slug)
    {
        $product = $this->productModel->getWithRelations()->where('products.slug', $slug)->first();
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'meta_title'       => $product['name'],
            'meta_description' => substr(strip_tags($product['description'] ?? ''), 0, 160),
            'product'          => $product,
            'images'           => $this->imageModel->getByProduct($product['id']),
            'rating'           => $this->reviewModel->getAverageRating($product['id']),
            'reviews'          => $this->reviewModel->getByProduct($product['id'], 5),
            'related'          => $this->productModel->getWithRelations()
                                ->where('products.category_id', $product['category_id'])
                                ->where('products.id !=', $product['id'])
                                ->where('products.is_active', 1)
                                ->findAll(4),
        ];

        return view('product/detail', $data);
    }

    /**
     * Halaman pencarian
     */
    public function search()
    {
        $page   = max(1, (int) $this->request->getGet('page'));
        $limit  = 12;
        $offset = ($page - 1) * $limit;

        $filters = [
            'q'            => $this->request->getGet('q') ?? '',
            'category_id'  => $this->request->getGet('category') ?? '',
            'min_price'    => $this->request->getGet('min_price') ?? '',
            'max_price'    => $this->request->getGet('max_price') ?? '',
            'city'         => $this->request->getGet('city') ?? '',
            'sort'         => $this->request->getGet('sort') ?? 'newest',
        ];

        $result = $this->productModel->searchProducts($filters, $limit, $offset);

        $data = [
            'meta_title'  => 'Hasil Pencarian: ' . esc($filters['q']),
            'search_query'=> $filters['q'],
            'products'    => $result['products'],
            'total'       => $result['total'],
            'page'        => $page,
            'limit'       => $limit,
            'filters'     => $filters,
            'categories'  => $this->categoryModel->getParents(),
        ];

        return view('product/search', $data);
    }

    /**
     * Pencarian AJAX (tanpa reload halaman)
     */
    public function searchAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $limit  = 12;
        $offset = max(0, (int) $this->request->getPost('offset'));

        $filters = [
            'q'            => $this->request->getPost('q') ?? '',
            'category_id'  => $this->request->getPost('category_id') ?? '',
            'min_price'    => $this->request->getPost('min_price') ?? '',
            'max_price'    => $this->request->getPost('max_price') ?? '',
            'city'         => $this->request->getPost('city') ?? '',
            'sort'         => $this->request->getPost('sort') ?? 'newest',
        ];

        $result = $this->productModel->searchProducts($filters, $limit, $offset);

        return $this->response->setJSON([
            'status'  => true,
            'data'    => $result['products'],
            'total'   => $result['total'],
            'hasMore' => ($offset + $limit) < $result['total'],
        ]);
    }

    /**
     * Produk berdasarkan kategori slug
     */
    public function byCategory($slug)
    {
        $category = $this->categoryModel->findBySlug($slug);
        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $page   = max(1, (int) $this->request->getGet('page'));
        $limit  = 12;
        $offset = ($page - 1) * $limit;

        $result = $this->productModel->searchProducts([
            'category_id' => $category['id'],
            'sort'        => $this->request->getGet('sort') ?? 'newest',
        ], $limit, $offset);

        $data = [
            'meta_title' => 'Kategori: ' . $category['name'],
            'category'   => $category,
            'products'   => $result['products'],
            'total'      => $result['total'],
            'page'       => $page,
            'limit'      => $limit,
            'categories' => $this->categoryModel->getParents(),
        ];

        return view('product/search', $data);
    }
}
