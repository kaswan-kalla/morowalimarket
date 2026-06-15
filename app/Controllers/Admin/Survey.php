<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SurveyModel;

class Survey extends BaseController
{
    public function index()
    {
        $data = [
            'meta_title' => 'Data Survey',
        ];
        return view('admin/survey/index', $data);
    }

    public function data()
    {
        $model = new SurveyModel();
        $db = \Config\Database::connect();

        // Total responden
        $total = $model->countAllResults();

        // Per desa
        $query = $db->query("SELECT alamat, COUNT(*) as jumlah FROM surveys GROUP BY alamat ORDER BY jumlah DESC");
        $perDesa = $query->getResultArray();

        // Status menikah
        $query = $db->query("SELECT status_menikah, COUNT(*) as jumlah FROM surveys GROUP BY status_menikah");
        $perStatus = $query->getResultArray();

        // Data terbaru
        $latest = $model->orderBy('created_at', 'DESC')->findAll(50);

        // Rata-rata pengeluaran
        $query = $db->query("SELECT AVG(pengeluaran_perbulan) as rata FROM surveys");
        $avgPengeluaran = $query->getRow()->rata ?? 0;

        return $this->response->setJSON([
            'status' => true,
            'total' => (int)$total,
            'per_desa' => $perDesa,
            'per_status' => $perStatus,
            'latest' => $latest,
            'avg_pengeluaran' => round($avgPengeluaran),
        ]);
    }
}
