<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Laporan Transaksi<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <h4 class="mb-4">Laporan Transaksi</h4>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form id="filterForm" class="row g-3 align-items-end">
                    <div class="col-md-3"><label class="form-label">Dari Tanggal</label><input type="date" name="start_date" class="form-control" value="<?= date('Y-m-01') ?>"></div>
                    <div class="col-md-3"><label class="form-label">Sampai Tanggal</label><input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="pending">Pending</option>
                            <option value="awaiting_payment">Menunggu Bayar</option>
                            <option value="processing">Diproses</option>
                            <option value="shipped">Dikirim</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-3"><button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filter</button></div>
                </form>
            </div>
        </div>

        <!-- Summary -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center"><small class="text-muted">Total Transaksi</small>
                        <h4 class="fw-bold text-primary" id="trxCount">0</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center"><small class="text-muted">Total Nilai</small>
                        <h4 class="fw-bold text-success" id="trxValue">Rp 0</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center"><small class="text-muted">Pembayaran Terverifikasi</small>
                        <h4 class="fw-bold text-info" id="trxVerified">0</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pembeli</th>
                                <th>Toko</th>
                                <th>Metode Bayar</th>
                                <th>Total</th>
                                <th>Status Bayar</th>
                                <th>Status Pesanan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody id="trxBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
    function loadTransactions() {
        $.get('<?= base_url('admin/reports/transaction-data') ?>', $('#filterForm').serialize(), function(res) {
            let d = res.data;
            $('#trxCount').text(d.total_count || 0);
            $('#trxValue').text('Rp ' + new Intl.NumberFormat('id-ID').format(d.total_value || 0));
            $('#trxVerified').text(d.verified_count || 0);

            let html = '';
            (d.transactions || []).forEach(function(t) {
                html += `<tr>
                <td><strong>${t.order_number}</strong></td>
                <td>${t.buyer_name}</td><td>${t.store_name || '-'}</td>
                <td>${t.payment_method ? t.payment_method.toUpperCase() : '-'}</td>
                <td class="text-danger fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(t.total_amount)}</td>
                <td><span class="badge bg-${t.payment_status === 'verified' ? 'success' : (t.payment_status === 'rejected' ? 'danger' : 'warning')}">${t.payment_status || '-'}</span></td>
                <td><span class="badge bg-${t.status === 'completed' ? 'success' : (t.status === 'cancelled' ? 'danger' : 'warning')}">${t.status}</span></td>
                <td>${new Date(t.created_at).toLocaleDateString('id-ID')}</td></tr>`;
            });
            $('#trxBody').html(html || '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
        });
    }

    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadTransactions();
    });
    $(document).ready(loadTransactions);
</script>
<?= $this->endSection() ?>