<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Dashboard Penjual<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <h4 class="mb-4">Dashboard Penjual</h4>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small>Total Produk</small>
                                <h4 class="fw-bold mb-0"><?= $stats['total_products'] ?? 0 ?></h4>
                            </div>
                            <i class="bi bi-box fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small>Total Pesanan</small>
                                <h4 class="fw-bold mb-0"><?= $stats['total_orders'] ?? 0 ?></h4>
                            </div>
                            <i class="bi bi-receipt fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small>Pesanan Baru</small>
                                <h4 class="fw-bold mb-0"><?= $stats['new_orders'] ?? 0 ?></h4>
                            </div>
                            <i class="bi bi-clock fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small>Omzet Bulan Ini</small>
                                <h5 class="fw-bold mb-0">Rp <?= number_format($stats['monthly_revenue'] ?? 0, 0, ',', '.') ?></h5>
                            </div>
                            <i class="bi bi-cash fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Grafik Penjualan (30 Hari)</h6>
                <canvas id="salesChart" height="80"></canvas>
            </div>
        </div>

        <div class="row g-3">
            <!-- Latest Products -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h6 class="fw-bold mb-0">Produk Terbaru</h6>
                            <a href="<?= base_url('seller/products') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                        <?php if (!empty($latest_products)): ?>
                            <?php foreach ($latest_products as $p): ?>
                                <div class="d-flex align-items-center gap-3 border-bottom pb-2 mb-2">
                                    <img src="<?= base_url('uploads/products/' . ($p['main_image'] ?? 'default.png')) ?>"
                                         class="rounded" width="50" height="50" style="object-fit:cover">
                                    <div class="flex-grow-1">
                                        <p class="mb-0 small fw-semibold"><?= esc($p['name']) ?></p>
                                        <small class="text-muted">Stok: <?= $p['stock'] ?></small>
                                    </div>
                                    <span class="text-danger small fw-bold">
                                        Rp <?= number_format($p['discount_price'] > 0 ? $p['discount_price'] : $p['price'], 0, ',', '.') ?>
                                    </span>
                                </div>
                            <?php endforeach ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-3">Belum ada produk</p>
                        <?php endif ?>
                    </div>
                </div>
            </div>

            <!-- Latest Orders -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h6 class="fw-bold mb-0">Pesanan Terbaru</h6>
                            <a href="<?= base_url('seller/orders') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                        <?php if (!empty($latest_orders)): ?>
                            <?php foreach ($latest_orders as $o): ?>
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                    <div>
                                        <p class="mb-0 small fw-semibold"><?= esc($o['order_number']) ?></p>
                                        <small class="text-muted"><?= date('d M Y', strtotime($o['created_at'])) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-<?= $o['status'] == 'completed' ? 'success' : 'warning' ?>">
                                            <?= ucfirst(str_replace('_', ' ', $o['status'])) ?>
                                        </span>
                                        <br><small class="text-danger fw-bold">Rp <?= number_format($o['total_amount'], 0, ',', '.') ?></small>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-3">Belum ada pesanan</p>
                        <?php endif ?>
                    </div>
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
// Sales Chart
let chartData = <?= json_encode($chart_data ?? []) ?>;
if (chartData && chartData.length > 0) {
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: chartData.map(d => d.date),
            datasets: [{
                label: 'Penjualan (Rp)',
                data: chartData.map(d => d.total),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102,126,234,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
}
</script>
<?= $this->endSection() ?>
