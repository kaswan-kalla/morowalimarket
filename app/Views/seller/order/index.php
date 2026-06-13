<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Pesanan Masuk<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <h4 class="mb-4">Pesanan Masuk</h4>

        <!-- Status filter -->
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link active" data-status="all" href="#">Semua</a></li>
            <li class="nav-item"><a class="nav-link" data-status="awaiting_payment" href="#">Menunggu Bayar</a></li>
            <li class="nav-item"><a class="nav-link" data-status="processing" href="#">Diproses</a></li>
            <li class="nav-item"><a class="nav-link" data-status="shipped" href="#">Dikirim</a></li>
            <li class="nav-item"><a class="nav-link" data-status="completed" href="#">Selesai</a></li>
        </ul>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pembeli</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $o): ?>
                                    <tr class="order-row" data-status="<?= $o['status'] ?>">
                                        <td><strong><?= esc($o['order_number']) ?></strong></td>
                                        <td><?= esc($o['buyer_name'] ?? '-') ?></td>
                                        <td class="text-danger fw-bold">Rp <?= number_format($o['total_amount'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $o['status'] == 'completed' ? 'success' : ($o['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst(str_replace('_', ' ', $o['status'])) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y H:i', strtotime($o['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('seller/orders/' . $o['id']) ?>" class="btn btn-outline-primary">Detail</a>
                                                <?php if ($o['status'] == 'processing'): ?>
                                                    <button class="btn btn-success" onclick="shipOrder(<?= $o['id'] ?>)">Kirim</button>
                                                <?php endif ?>
                                                <?php if ($o['status'] == 'awaiting_payment'): ?>
                                                    <button class="btn btn-warning btn-sm" onclick="processOrder(<?= $o['id'] ?>)">Proses</button>
                                                <?php endif ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada pesanan</td></tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pager)): ?>
                    <div class="d-flex justify-content-center"><?= $pager->links('default', 'default_full') ?></div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
$('.nav-tabs .nav-link').on('click', function(e) {
    e.preventDefault();
    $('.nav-tabs .nav-link').removeClass('active');
    $(this).addClass('active');
    let s = $(this).data('status');
    if (s === 'all') { $('.order-row').show(); }
    else { $('.order-row').hide(); $('.order-row[data-status="' + s + '"]').show(); }
});

function processOrder(id) {
    $.post('<?= base_url('seller/orders/process') ?>', { id: id }, function(res) {
        if (res.status) { showToast('Pesanan diproses', 'success'); location.reload(); }
        else showToast(res.message, 'danger');
    });
}

function shipOrder(id) {
    Swal.fire({
        title: 'Input No. Resi', input: 'text', inputPlaceholder: 'Masukkan nomor resi',
        showCancelButton: true, confirmButtonText: 'Kirim', cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed && result.value) {
            $.post('<?= base_url('seller/orders/ship') ?>', { id: id, tracking_number: result.value }, function(res) {
                if (res.status) { showToast('Pesanan dikirim', 'success'); location.reload(); }
                else showToast(res.message, 'danger');
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
