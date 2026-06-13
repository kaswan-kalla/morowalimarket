<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class User extends BaseController
{
    protected $userModel;
    public function __construct() { $this->userModel = new UserModel(); }

    public function index() { return view('admin/user/index', ['meta_title' => 'Manajemen User']); }

    /** Data untuk DataTable (server-side) */
    public function data()
    {
        $search = $this->request->getGet('search')['value'] ?? '';
        $start  = (int) $this->request->getGet('start');
        $limit  = (int) $this->request->getGet('length');

        $builder = $this->userModel->builder();
        if ($search) {
            $builder->groupStart()->like('name', $search)->orLike('email', $search)->groupEnd();
        }

        $total = (clone $builder)->countAllResults(false);
        $users = $builder->orderBy('created_at', 'DESC')->get($limit, $start)->getResultArray();

        return $this->response->setJSON([
            'draw'            => (int) $this->request->getGet('draw'),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $users,
        ]);
    }

    /** Toggle status aktif/nonaktif */
    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);
        $user = $this->userModel->find($id);
        if (!$user) return $this->response->setJSON(['status' => false, 'message' => 'User tidak ditemukan']);

        $this->userModel->update($id, ['is_active' => !$user['is_active']]);
        return $this->response->setJSON(['status' => true, 'message' => 'Status user diperbarui']);
    }
}
