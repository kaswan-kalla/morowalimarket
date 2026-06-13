<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

/**
 * CRUD Kategori (Admin)
 */
class Category extends BaseController
{
    protected $categoryModel;
    public function __construct() { $this->categoryModel = new CategoryModel(); }

    public function index()
    {
        $data = [
            'meta_title' => 'Manajemen Kategori',
            'categories' => $this->categoryModel->getWithProductCount(),
        ];
        return view('admin/category/index', $data);
    }

    public function data()
    {
        return $this->response->setJSON([
            'status' => true,
            'data'   => $this->categoryModel->getWithProductCount(),
        ]);
    }

    /** Simpan kategori baru (AJAX) */
    public function save()
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $rules = ['name' => 'required|min_length[2]|max_length[100]'];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => false, 'message' => implode('<br>', $this->validator->getErrors())]);
        }

        $name = $this->request->getPost('name');
        $slug = generate_slug($name);
        if ($this->categoryModel->findBySlug($slug)) $slug .= '-' . time();

        $icon = null;
        $file = $this->request->getFile('icon');
        if ($file && $file->isValid()) {
            $icon = upload_image($file, 'uploads/categories');
        }

        $this->categoryModel->insert([
            'name'      => $name,
            'slug'      => $slug,
            'icon'      => $icon,
            'parent_id' => $this->request->getPost('parent_id') ?: null,
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Kategori ditambahkan']);
    }

    /** Update kategori (AJAX) */
    public function update($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $rules = ['name' => 'required|min_length[2]|max_length[100]'];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => false, 'message' => implode('<br>', $this->validator->getErrors())]);
        }

        $data = ['name' => $this->request->getPost('name')];

        $file = $this->request->getFile('icon');
        if ($file && $file->isValid()) {
            $old = $this->categoryModel->find($id);
            if ($old['icon']) delete_image($old['icon']);
            $data['icon'] = upload_image($file, 'uploads/categories');
        }

        $this->categoryModel->update($id, $data);
        return $this->response->setJSON(['status' => true, 'message' => 'Kategori diperbarui']);
    }

    /** Hapus kategori (soft delete) */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);
        $this->categoryModel->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Kategori dihapus']);
    }
}
