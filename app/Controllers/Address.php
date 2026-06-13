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
            'meta_title' => 'Alamat Saya',
            'addresses'  => $this->addressModel->getByUser($this->session->get('user_id')),
        ];
        return view('address/index', $data);
    }

    /**
     * Simpan alamat baru/edit (AJAX)
     */
    public function save()
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $userId  = $this->session->get('user_id');
        $id      = (int) $this->request->getPost('id');
        $isDefault = (int) $this->request->getPost('is_default', 0);

        $rules = [
            'name'           => 'required|max_length[100]',
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
            'name'           => $this->request->getPost('name'),
            'recipient_name' => $this->request->getPost('recipient_name'),
            'phone'          => $this->request->getPost('phone'),
            'address'        => $this->request->getPost('address'),
            'city'           => $this->request->getPost('city'),
            'province'       => $this->request->getPost('province'),
            'postal_code'    => $this->request->getPost('postal_code'),
            'is_default'     => $isDefault,
        ];

        if ($id > 0) {
            $this->addressModel->update($id, $data);
        } else {
            $id = $this->addressModel->insert($data);
        }

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
