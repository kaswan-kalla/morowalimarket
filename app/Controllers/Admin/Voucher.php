<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VoucherModel;

/**
 * CRUD Voucher (Admin)
 */
class Voucher extends BaseController
{
    protected $voucherModel;
    public function __construct() { $this->voucherModel = new VoucherModel(); }

    public function index()
    {
        return view('admin/voucher/index', [
            'meta_title' => 'Manajemen Voucher',
            'vouchers'   => $this->voucherModel->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function data()
    {
        return $this->response->setJSON([
            'status' => true,
            'data'   => $this->voucherModel->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $rules = [
            'code'           => 'required|max_length[50]|is_unique[vouchers.code]',
            'discount_type'  => 'required|in_list[percentage,fixed]',
            'discount_value' => 'required|numeric|greater_than[0]',
            'start_date'     => 'required|valid_date',
            'expired_at'     => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => false, 'message' => implode('<br>', $this->validator->getErrors())]);
        }

        $this->voucherModel->insert([
            'code'           => strtoupper($this->request->getPost('code')),
            'discount_type'  => $this->request->getPost('discount_type'),
            'discount_value' => $this->request->getPost('discount_value'),
            'min_purchase'   => $this->request->getPost('min_purchase', 0),
            'max_discount'   => $this->request->getPost('max_discount') ?: null,
            'max_usage'      => $this->request->getPost('max_usage', 0),
            'start_date'     => $this->request->getPost('start_date'),
            'expired_at'     => $this->request->getPost('expired_at'),
            'is_active'      => $this->request->getPost('is_active', 1),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Voucher ditambahkan']);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $this->voucherModel->update($id, [
            'discount_type'  => $this->request->getPost('discount_type'),
            'discount_value' => $this->request->getPost('discount_value'),
            'min_purchase'   => $this->request->getPost('min_purchase', 0),
            'max_discount'   => $this->request->getPost('max_discount') ?: null,
            'max_usage'      => $this->request->getPost('max_usage', 0),
            'start_date'     => $this->request->getPost('start_date'),
            'expired_at'     => $this->request->getPost('expired_at'),
            'is_active'      => $this->request->getPost('is_active', 1),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Voucher diperbarui']);
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);
        $this->voucherModel->delete($id);
        return $this->response->setJSON(['status' => true, 'message' => 'Voucher dihapus']);
    }
}
