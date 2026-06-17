<?php

namespace App\Controllers\Seller;

use App\Controllers\BaseController;
use App\Models\StoreModel;
use App\Models\ProductModel;
use App\Models\ProductImageModel;
use App\Models\CategoryModel;

/**
 * CRUD Produk untuk Seller
 */
class Product extends BaseController
{
    protected $store, $productModel, $imageModel, $categoryModel;

    public function __construct()
    {
        $storeModel = new StoreModel();
        $this->store = $storeModel->findByUserId(session()->get('user_id'));
        $this->productModel  = new ProductModel();
        $this->imageModel    = new ProductImageModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        if (!$this->store) return redirect()->to('/seller/toko');

        $data = [
            'meta_title' => 'Produk Saya',
            'products'   => $this->productModel->where('store_id', $this->store['id'])->orderBy('created_at', 'DESC')->findAll(),
        ];
        return view('seller/product/index', $data);
    }

    public function create()
    {
        if (!$this->store) return redirect()->to('/seller/toko');
        return view('seller/product/form', [
            'meta_title' => 'Tambah Produk',
            'categories' => $this->categoryModel->getParents(),
            'product'    => null,
        ]);
    }

    /**
     * Simpan produk baru (AJAX)
     */
    public function save()
    {
        if (!$this->request->isAJAX() || !$this->store) {
            return $this->response->setJSON(['status' => false]);
        }

        $rules = [
            'name'        => 'required|min_length[3]|max_length[200]',
            'category_id' => 'required|numeric',
            'price'       => 'required|numeric|greater_than[0]',
            'stock'       => 'required|integer|greater_than_equal_to[0]',
            'weight'      => 'required|numeric|greater_than[0]',
            'description' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => false, 'message' => implode('<br>', $this->validator->getErrors())]);
        }

        $name = $this->request->getPost('name');
        $slug = generate_slug($name);

        // Pastikan slug unik
        $existingSlug = $this->productModel->where('slug', $slug)->first();
        if ($existingSlug) {
            $slug .= '-' . time();
        }

        $productId = $this->productModel->insert([
            'store_id'       => $this->store['id'],
            'category_id'    => $this->request->getPost('category_id'),
            'name'           => $name,
            'slug'           => $slug,
            'sku'            => $this->request->getPost('sku'),
            'description'    => $this->request->getPost('description'),
            'price'          => $this->request->getPost('price'),
            'discount_price' => $this->request->getPost('discount_price') ?: null,
            'weight'         => $this->request->getPost('weight'),
            'stock'          => $this->request->getPost('stock'),
            'is_active'      => 1,
        ]);

        return $this->response->setJSON([
            'status'   => true,
            'message'  => 'Produk berhasil ditambahkan',
            'redirect' => base_url('seller/produk/edit/' . $productId),
        ]);
    }

    public function edit($id)
    {
        if (!$this->store) return redirect()->to('/seller/toko');

        $product = $this->productModel->find($id);
        if (!$product || $product['store_id'] != $this->store['id']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'meta_title' => 'Edit Produk',
            'product'    => $product,
            'images'     => $this->imageModel->getByProduct($id),
            'categories' => $this->categoryModel->getParents(),
        ];
        return view('seller/product/form', $data);
    }

    /**
     * Update produk (AJAX)
     */
    public function update($id)
    {
        if (!$this->request->isAJAX() || !$this->store) {
            return $this->response->setJSON(['status' => false]);
        }

        $product = $this->productModel->find($id);
        if (!$product || $product['store_id'] != $this->store['id']) {
            return $this->response->setJSON(['status' => false, 'message' => 'Produk tidak ditemukan']);
        }

        $rules = [
            'name'        => 'required|min_length[3]|max_length[200]',
            'category_id' => 'required|numeric',
            'price'       => 'required|numeric|greater_than[0]',
            'stock'       => 'required|integer|greater_than_equal_to[0]',
            'weight'      => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => false, 'message' => implode('<br>', $this->validator->getErrors())]);
        }

        $this->productModel->update($id, [
            'name'           => $this->request->getPost('name'),
            'category_id'    => $this->request->getPost('category_id'),
            'sku'            => $this->request->getPost('sku'),
            'description'    => $this->request->getPost('description'),
            'price'          => $this->request->getPost('price'),
            'discount_price' => $this->request->getPost('discount_price') ?: null,
            'weight'         => $this->request->getPost('weight'),
            'stock'          => $this->request->getPost('stock'),
            'is_active'      => $this->request->getPost('is_active', 1),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Produk berhasil diperbarui']);
    }

    /**
     * Hapus produk (soft delete)
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX() || !$this->store) {
            return $this->response->setJSON(['status' => false]);
        }

        $product = $this->productModel->find($id);
        if (!$product || $product['store_id'] != $this->store['id']) {
            return $this->response->setJSON(['status' => false, 'message' => 'Produk tidak ditemukan']);
        }

        $this->productModel->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Produk dihapus']);
    }

    /**
     * Upload gambar produk (AJAX) - multi image
     */
    public function uploadImage()
    {
        if (!$this->request->isAJAX() || !$this->store) {
            return $this->response->setJSON(['status' => false]);
        }

        $productId = (int) $this->request->getPost('product_id');
        $product = $this->productModel->find($productId);
        if (!$product || $product['store_id'] != $this->store['id']) {
            return $this->response->setJSON(['status' => false, 'message' => 'Produk tidak valid']);
        }

        // Max 5 gambar per produk
        if ($this->imageModel->countByProduct($productId) >= 5) {
            return $this->response->setJSON(['status' => false, 'message' => 'Maksimal 5 gambar per produk']);
        }

        $file = $this->request->getFile('image');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pilih file gambar']);
        }

        $imagePath = upload_image($file, 'uploads/products');
        $isMain = $this->imageModel->countByProduct($productId) === 0 ? 1 : 0;

        $imageId = $this->imageModel->insert([
            'product_id' => $productId,
            'image'      => $imagePath,
            'is_main'    => $isMain,
        ]);

        // Set main_image di products jika ini gambar utama
        if ($isMain) {
            $this->productModel->update($productId, ['main_image' => $imagePath]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Gambar berhasil diupload',
            'data'   => ['id' => $imageId, 'image' => $imagePath, 'is_main' => $isMain]
        ]);
    }

    /**
     * Hapus gambar produk (AJAX)
     */
    public function deleteImage($id)
    {
        if (!$this->request->isAJAX() || !$this->store) {
            return $this->response->setJSON(['status' => false]);
        }

        $image = $this->imageModel->find($id);
        if (!$image) return $this->response->setJSON(['status' => false]);

        $product = $this->productModel->find($image['product_id']);
        if (!$product || $product['store_id'] != $this->store['id']) {
            return $this->response->setJSON(['status' => false]);
        }

        delete_image($image['image']);
        $this->imageModel->delete($id);

        return $this->response->setJSON(['status' => true, 'message' => 'Gambar dihapus']);
    }

    /**
     * Set gambar utama (AJAX)
     */
    public function setMainImage($id)
    {
        if (!$this->request->isAJAX() || !$this->store) {
            return $this->response->setJSON(['status' => false]);
        }

        $image = $this->imageModel->find($id);
        if (!$image) return $this->response->setJSON(['status' => false]);

        $product = $this->productModel->find($image['product_id']);
        if (!$product || $product['store_id'] != $this->store['id']) {
            return $this->response->setJSON(['status' => false]);
        }

        $this->imageModel->setMain($id, $image['product_id']);
        return $this->response->setJSON(['status' => true, 'message' => 'Gambar utama diperbarui']);
    }
}
