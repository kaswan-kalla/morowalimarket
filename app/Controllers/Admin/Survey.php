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

        // Filter
        $filterInvestasi = $this->request->getGet('siap_investasi') ?: '';
        $where = '';
        $bind  = [];
        if ($filterInvestasi !== '') {
            $where = 'WHERE siap_investasi = :investasi:';
            $bind  = ['investasi' => $filterInvestasi];
        }

        // Total responden
        $total = $model->countAllResults();

        // Per desa
        $query = $db->query("SELECT alamat, COUNT(*) as jumlah FROM surveys $where GROUP BY alamat ORDER BY jumlah DESC", $bind);
        $perDesa = $query->getResultArray();

        // Status menikah
        $query = $db->query("SELECT status_menikah, COUNT(*) as jumlah FROM surveys $where GROUP BY status_menikah", $bind);
        $perStatus = $query->getResultArray();

        // Rentang pengeluaran
        $query = $db->query("SELECT pengeluaran_perbulan, COUNT(*) as jumlah FROM surveys $where GROUP BY pengeluaran_perbulan ORDER BY FIELD(pengeluaran_perbulan, 'Dibawah 1jt','1jt - 2jt','2jt - 3jt','Diatas 3jt')", $bind);
        $perPengeluaran = $query->getResultArray();

        // Preferensi belanja
        $query = $db->query("SELECT preferensi_belanja, COUNT(*) as jumlah FROM surveys $where GROUP BY preferensi_belanja", $bind);
        $perPreferensi = $query->getResultArray();

        // Siap investasi
        $query = $db->query("SELECT siap_investasi, COUNT(*) as jumlah FROM surveys $where GROUP BY siap_investasi", $bind);
        $perInvestasi = $query->getResultArray();

        // Siap member (selalu hitung global)
        $siapMember = $model->where('siap_member', 1)->countAllResults();

        // Data terbaru
        if ($filterInvestasi !== '') {
            $model->where('siap_investasi', $filterInvestasi);
        }
        $latest = $model->orderBy('created_at', 'DESC')->findAll(50);

        return $this->response->setJSON([
            'status' => true,
            'total' => (int)$total,
            'per_desa' => $perDesa,
            'per_status' => $perStatus,
            'per_pengeluaran' => $perPengeluaran,
            'per_preferensi'   => $perPreferensi,
            'per_investasi'    => $perInvestasi,
            'siap_member'      => (int)$siapMember,
            'latest' => $latest,
        ]);
    }
}
