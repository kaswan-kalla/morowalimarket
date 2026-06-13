<?php

namespace App\Controllers\Seller;

use App\Controllers\BaseController;
use App\Models\StoreModel;
use App\Models\UserModel;

/**
 * Pengaturan Toko Seller (buat / edit toko)
 */
class Store extends BaseController
{
    protected $storeModel;

    public function __construct()
    {
        $this->storeModel = new StoreModel();
    }

    /**
     * Halaman pengaturan toko
     */
    public function index()
    {
        $userId = $this->session->get('user_id');
        $store  = $this->storeModel->findByUserId($userId);

        return view('seller/store/index', [
            'meta_title' => $store ? 'Pengaturan Toko' : 'Buka Toko',
            'store'      => $store,
        ]);
    }

    /**
     * Simpan/update toko (AJAX)
     */
    public function update()
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $userId = $this->session->get('user_id');
        $existing = $this->storeModel->findByUserId($userId);

        $rules = [
            'name'        => 'required|min_length[3]|max_length[150]',
            'description' => 'permit_empty',
            'address'     => 'permit_empty',
            'city'        => 'permit_empty|max_length[100]',
            'province'    => 'permit_empty|max_length[100]',
            'phone'       => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => false, 'message' => implode('<br>', $this->validator->getErrors())]);
        }

        $name = $this->request->getPost('name');
        $slug = generate_slug($name);

        $data = [
            'name'        => $name,
            'description' => $this->request->getPost('description'),
            'address'     => $this->request->getPost('address'),
            'city'        => $this->request->getPost('city'),
            'province'    => $this->request->getPost('province'),
            'postal_code' => $this->request->getPost('postal_code'),
            'phone'       => $this->request->getPost('phone'),
            'is_open'     => $this->request->getPost('is_open', 1),
        ];

        // Upload logo jika ada
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid()) {
            if ($existing && $existing['logo']) delete_image($existing['logo']);
            $data['logo'] = upload_image($logo, 'uploads/stores');
        }

        // Upload banner jika ada
        $banner = $this->request->getFile('banner');
        if ($banner && $banner->isValid()) {
            if ($existing && $existing['banner']) delete_image($existing['banner']);
            $data['banner'] = upload_image($banner, 'uploads/stores');
        }

        if ($existing) {
            $this->storeModel->update($existing['id'], $data);
        } else {
            // Slug unik
            if ($this->storeModel->findBySlug($slug)) {
                $slug .= '-' . time();
            }
            $data['slug']    = $slug;
            $data['user_id'] = $userId;
            $this->storeModel->insert($data);

            // Update role user jadi seller
            $userModel = new UserModel();
            $userModel->update($userId, ['role' => 'seller']);
            $this->session->set('role', 'seller');
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Toko berhasil disimpan']);
    }
}
