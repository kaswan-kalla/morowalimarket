<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Laporan Penjualan<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <h4 class="mb-4">Laporan Penjualan</h4>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form id="filterForm" class="row g-3 align-items-end">
                    <div class="col-md-3"><label class="form-label">Dari Tanggal</label><input type="date" name="start_date" class="form-control" value="<?= date('Y-m-01') ?>"></div>
                    <div class="col-md-3"><label class="form-label">Sampai Tanggal</label><input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                    <div class="col-md-3"><button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filter</button></div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4" id="summaryCards">
            <div class="col-md-3"><div class="card shadow-sm bg-success text-white"><div class="card-body"><small>Total Penjualan</small><h4 class="fw-bold" id="totalSales">Rp 0</h4></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm bg-primary text-white"><div class="card-body"><small>Total Pesanan</small><h4 class="fw-bold" id="totalOrders">0</h4></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm bg-warning text-white"><div class="card-body"><small>Rata-rata per Pesanan</small><h4 class="fw-bold" id="avgOrder">Rp 0</h4></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm bg-info text-white"><div class="card-body"><small>Produk Terjual</small><h4 class="fw-bold" id="totalItems">0</h4></div></div></div>
        </div>

        <!-- Chart -->
        <div class="card shadow-sm mb-4">
            <div class="card-body"><h6 class="fw-bold mb-3">Grafik Penjualan</h6><canvas id="salesChart" height="80"></canvas></div>
        </div>

        <!-- Detail Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Detail Transaksi</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light"><tr><th>No. Pesanan</th><th>Pembeli</th><th>Toko</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
                        <tbody id="reportBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
let chart = null;

function loadReport() {
    let params = $('#filterForm').serialize();
    $.get('<?= base_url('admin/reports/sales-data') ?>', params, function(res) {
        let d = res.data;
        $('#totalSales').text('Rp ' + new Intl.NumberFormat('id-ID').format(d.total_sales || 0));
        $('#totalOrders').text(d.total_orders || 0);
        $('#avgOrder').text('Rp ' + new Intl.NumberFormat('id-ID').format(d.avg_order || 0));
        $('#totalItems').text(d.total_items || 0);

        let html = '';
        (d.orders || []).forEach(function(o) {
            html += `<tr><td>${o.order_number}</td><td>${o.buyer_name}</td><td>${o.store_name}</td>
                <td class="text-danger fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(o.total_amount)}</td>
                <td><span class="badge bg-${o.status === 'completed' ? 'success' : 'warning'}">${o.status}</span></td>
                <td>${new Date(o.created_at).toLocaleDateString('id-ID')}</td></tr>`;
        });
        $('#reportBody').html(html || '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');

        if (chart) chart.destroy();
        chart = new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: { labels: (d.chart || []).map(c => c.date), datasets: [{ label: 'Penjualan (Rp)', data: (d.chart || []).map(c => c.total), backgroundColor: '#667eea' }] },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    });
}

$('#filterForm').on('submit', function(e) { e.preventDefault(); loadReport(); });
$(document).ready(loadReport);
</script>
<?= $this->endSection() ?>
