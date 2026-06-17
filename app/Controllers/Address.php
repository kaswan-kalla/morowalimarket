<?php

namespace App\Controllers;

use App\Models\AddressModel;

/**
 * Controller Alamat User
 */
class Address extends BaseController
{
    protected $addressModel;

    public function __construct()
    {
        $this->addressModel = new AddressModel();
    }

    public function index()
    {
        $data = [
            'content'    => 'address',
            'meta_title' => 'Alamat Saya',
            'addresses'  => $this->addressModel->getByUser($this->session->get('user_id')),
        ];
        return view('layout/marketplace_content', $data);
    }

    /**
     * Ambil satu alamat (AJAX)
     */
    public function get($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $address = $this->addressModel->find($id);
        if (!$address || $address['user_id'] != $this->session->get('user_id')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Alamat tidak ditemukan']);
        }

        return $this->response->setJSON(['status' => true, 'data' => $address]);
    }

    /**
     * Simpan alamat baru (AJAX)
     */
    public function save()
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $userId  = $this->session->get('user_id');
        $isDefault = $this->request->getPost('is_default') ? 1 : 0;

        $rules = [
            'label'          => 'required|max_length[100]',
            'recipient_name' => 'required|max_length[100]',
            'phone'          => 'required|max_length[20]',
            'address'        => 'required',
            'city'           => 'required|max_length[100]',
            'province'       => 'required|max_length[100]',
            'postal_code'    => 'required|max_length[10]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => false, 'message' => implode('<br>', $this->validator->getErrors())]);
        }

        $data = [
            'user_id'        => $userId,
            'label'          => $this->request->getPost('label'),
            'recipient_name' => $this->request->getPost('recipient_name'),
            'phone'          => $this->request->getPost('phone'),
            'address'        => $this->request->getPost('address'),
            'city'           => $this->request->getPost('city'),
            'province'       => $this->request->getPost('province'),
            'postal_code'    => $this->request->getPost('postal_code'),
            'is_default'     => $isDefault,
        ];

        $id = $this->addressModel->insert($data);

        if ($isDefault) {
            $this->addressModel->setDefault($id, $userId);
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Alamat berhasil disimpan']);
    }

    /**
     * Update alamat (AJAX)
     */
    public function update($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $address = $this->addressModel->find($id);
        if (!$address || $address['user_id'] != $this->session->get('user_id')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Alamat tidak ditemukan']);
        }

        $userId  = $this->session->get('user_id');
        $isDefault = $this->request->getPost('is_default') ? 1 : 0;

        $rules = [
            'label'          => 'required|max_length[100]',
            'recipient_name' => 'required|max_length[100]',
            'phone'          => 'required|max_length[20]',
            'address'        => 'required',
            'city'           => 'required|max_length[100]',
            'province'       => 'required|max_length[100]',
            'postal_code'    => 'required|max_length[10]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => false, 'message' => implode('<br>', $this->validator->getErrors())]);
        }

        $data = [
            'label'          => $this->request->getPost('label'),
            'recipient_name' => $this->request->getPost('recipient_name'),
            'phone'          => $this->request->getPost('phone'),
            'address'        => $this->request->getPost('address'),
            'city'           => $this->request->getPost('city'),
            'province'       => $this->request->getPost('province'),
            'postal_code'    => $this->request->getPost('postal_code'),
            'is_default'     => $isDefault,
        ];

        $this->addressModel->update($id, $data);

        if ($isDefault) {
            $this->addressModel->setDefault($id, $userId);
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Alamat berhasil disimpan']);
    }

    /**
     * Hapus alamat (AJAX)
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $address = $this->addressModel->find($id);
        if (!$address || $address['user_id'] != $this->session->get('user_id')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Alamat tidak ditemukan']);
        }

        $this->addressModel->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Alamat dihapus']);
    }

    /**
     * Set alamat default (AJAX)
     */
    public function setDefault($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $this->addressModel->setDefault($id, $this->session->get('user_id'));
        return $this->response->setJSON(['status' => true, 'message' => 'Alamat default diperbarui']);
    }
}
