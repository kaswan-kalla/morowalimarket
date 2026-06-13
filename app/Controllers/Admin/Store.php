<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StoreModel;

class Store extends BaseController
{
    protected $storeModel;
    public function __construct() { $this->storeModel = new StoreModel(); }

    public function index() { return view('admin/store/index', ['meta_title' => 'Manajemen Toko']); }

    public function data()
    {
        $search = $this->request->getGet('search')['value'] ?? '';
        $start  = (int) $this->request->getGet('start');
        $limit  = (int) $this->request->getGet('length');

        $builder = $this->storeModel->select('stores.*, users.name as owner_name')
                    ->join('users', 'users.id = stores.user_id');
        if ($search) {
            $builder->groupStart()->like('stores.name', $search)->orLike('users.name', $search)->groupEnd();
        }

        $total = (clone $builder)->countAllResults(false);
        $stores = $builder->orderBy('stores.created_at', 'DESC')->get($limit, $start)->getResultArray();

        return $this->response->setJSON([
            'draw' => (int) $this->request->getGet('draw'),
            'recordsTotal' => $total, 'recordsFiltered' => $total, 'data' => $stores,
        ]);
    }
}
