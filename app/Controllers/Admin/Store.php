<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StoreModel;
use App\Models\UserModel;

class Store extends BaseController
{
    protected $storeModel, $userModel;

    public function __construct()
    {
        $this->storeModel = new StoreModel();
        $this->userModel  = new UserModel();
    }

    public function index()
    {
        return view('admin/store/index', [
            'meta_title' => 'Manajemen Toko',
        ]);
    }

    public function data()
    {
        $search = $this->request->getGet('search')['value'] ?? '';
        $start  = (int) $this->request->getGet('start');
        $limit  = (int) $this->request->getGet('length');

        $builder = $this->storeModel->select('stores.*, users.name as owner_name, (SELECT COUNT(*) FROM products WHERE products.store_id = stores.id AND products.deleted_at IS NULL) as product_count')
            ->join('users', 'users.id = stores.user_id');
        if ($search) {
            $builder->groupStart()->like('stores.name', $search)->orLike('users.name', $search)->groupEnd();
        }

        $total  = (clone $builder)->countAllResults(false);
        $stores = $builder->orderBy('stores.created_at', 'DESC')->get($limit, $start)->getResultArray();

        return $this->response->setJSON([
            'draw'            => (int) $this->request->getGet('draw'),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $stores,
        ]);
    }

    public function get($id)
    {
        $store = $this->storeModel->find($id);
        if (!$store) {
            return $this->response->setJSON(['status' => false, 'message' => 'Toko tidak ditemukan']);
        }
        return $this->response->setJSON(['status' => true, 'data' => $store]);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $name = $this->request->getPost('name');
        $slug = generate_slug($name);

        $existing = $this->storeModel->where('slug', $slug)->first();
        if ($existing) {
            $slug = $slug . '-' . time();
        }

        $data = [
            'user_id'     => (int) $this->request->getPost('user_id'),
            'name'        => $name,
            'slug'        => $slug,
            'description' => $this->request->getPost('description') ?: '',
            'address'     => $this->request->getPost('address') ?: '',
            'city'        => $this->request->getPost('city') ?: '',
            'province'    => $this->request->getPost('province') ?: '',
            'postal_code' => $this->request->getPost('postal_code') ?: '',
            'phone'       => $this->request->getPost('phone') ?: '',
            'is_open'     => $this->request->getPost('is_open') ? 1 : 0,
        ];

        if (!$this->storeModel->insert($data)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan: ' . implode(', ', $this->storeModel->errors())]);
        }

        $storeId = $this->storeModel->getInsertID();

        // Upload logo
        $file = $this->request->getFile('logo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $path = upload_image($file, 'uploads/stores');
            if ($path) {
                $this->storeModel->update($storeId, ['logo' => $path]);
            }
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Toko berhasil ditambahkan']);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $store = $this->storeModel->find($id);
        if (!$store) {
            return $this->response->setJSON(['status' => false, 'message' => 'Toko tidak ditemukan']);
        }

        $name = $this->request->getPost('name');
        $slug = generate_slug($name);

        $existing = $this->storeModel->where('slug', $slug)->where('id !=', $id)->first();
        if ($existing) {
            $slug = $slug . '-' . $id;
        }

        $data = [
            'user_id'     => (int) $this->request->getPost('user_id'),
            'name'        => $name,
            'slug'        => $slug,
            'description' => $this->request->getPost('description') ?: '',
            'address'     => $this->request->getPost('address') ?: '',
            'city'        => $this->request->getPost('city') ?: '',
            'province'    => $this->request->getPost('province') ?: '',
            'postal_code' => $this->request->getPost('postal_code') ?: '',
            'phone'       => $this->request->getPost('phone') ?: '',
            'is_open'     => $this->request->getPost('is_open') ? 1 : 0,
        ];

        if (!$this->storeModel->update($id, $data)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengupdate: ' . implode(', ', $this->storeModel->errors())]);
        }

        // Upload logo baru
        $file = $this->request->getFile('logo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($store['logo']) {
                delete_image($store['logo']);
            }
            $path = upload_image($file, 'uploads/stores');
            if ($path) {
                $this->storeModel->update($id, ['logo' => $path]);
            }
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Toko berhasil diperbarui']);
    }

    public function toggle()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }
        $id = (int) $this->request->getPost('id');
        $store = $this->storeModel->find($id);
        if (!$store) {
            return $this->response->setJSON(['status' => false, 'message' => 'Toko tidak ditemukan']);
        }

        $this->storeModel->update($id, ['is_open' => !$store['is_open']]);
        return $this->response->setJSON(['status' => true, 'message' => 'Status toko diperbarui']);
    }

    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $id = (int) $this->request->getPost('id');
        $store = $this->storeModel->find($id);
        if (!$store) {
            return $this->response->setJSON(['status' => false, 'message' => 'Toko tidak ditemukan']);
        }

        // Hapus logo
        if ($store['logo']) {
            delete_image($store['logo']);
        }

        $this->storeModel->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Toko berhasil dihapus']);
    }

    public function getUserOption()
    {
        $users = $this->userModel->select('id, name, email')->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
        $options = [['DisplayText' => '-- Pilih Pemilik --', 'Value' => '']];
        foreach ($users as $u) {
            $options[] = ['DisplayText' => $u['name'] . ' (' . $u['email'] . ')', 'Value' => (int) $u['id']];
        }
        return $this->response->setJSON(['Result' => 'OK', 'Options' => $options]);
    }
}
