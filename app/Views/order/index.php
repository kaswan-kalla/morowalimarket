<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Pesanan Saya<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>

<div class="container my-4">
    <h4 class="mb-4"><i class="bi bi-receipt"></i> Pesanan Saya</h4>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="orderTabs">
        <li class="nav-item"><a class="nav-link active" data-status="all" href="#">Semua</a></li>
        <li class="nav-item"><a class="nav-link" data-status="pending" href="#">Pending</a></li>
        <li class="nav-item"><a class="nav-link" data-status="awaiting_payment" href="#">Menunggu Bayar</a></li>
        <li class="nav-item"><a class="nav-link" data-status="processing" href="#">Diproses</a></li>
        <li class="nav-item"><a class="nav-link" data-status="shipped" href="#">Dikirim</a></li>
        <li class="nav-item"><a class="nav-link" data-status="completed" href="#">Selesai</a></li>
    </ul>

    <div id="orderList">
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $o): ?>
                <div class="card shadow-sm mb-3 order-item" data-status="<?= $o['status'] ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <small class="text-muted">No. Pesanan</small>
                                <p class="mb-0 fw-bold"><?= esc($o['order_number']) ?></p>
                            </div>
                            <span class="badge bg-<?= $o['status'] == 'completed' ? 'success' : ($o['status'] == 'cancelled' ? 'danger' : 'warning') ?> px-3 py-2">
                                <?= ucfirst(str_replace('_', ' ', $o['status'])) ?>
                            </span>
                        </div>
                        <hr>
                        <?php if (!empty($o['items'])): ?>
                            <?php foreach ($o['items'] as $item): ?>
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <img src="<?= base_url('uploads/products/' . ($item['image'] ?? 'default.png')) ?>"
                                         class="rounded" width="60" height="60" style="object-fit:cover">
                                    <div class="flex-grow-1">
                                        <p class="mb-0 small fw-semibold"><?= esc($item['product_name']) ?></p>
                                        <small class="text-muted"><?= $item['qty'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></small>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php endif ?>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total: <span class="text-danger">Rp <?= number_format($o['total_amount'], 0, ',', '.') ?></span></span>
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('order/' . $o['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                <?php if ($o['status'] == 'awaiting_payment'): ?>
                                    <a href="<?= base_url('payment/' . $o['id']) ?>" class="btn btn-sm btn-primary">Bayar</a>
                                <?php endif ?>
                                <?php if ($o['status'] == 'pending' || $o['status'] == 'awaiting_payment'): ?>
                                    <button class="btn btn-sm btn-outline-danger" onclick="cancelOrder(<?= $o['id'] ?>)">Batal</button>
                                <?php endif ?>
                                <?php if ($o['status'] == 'shipped'): ?>
                                    <button class="btn btn-sm btn-success" onclick="completeOrder(<?= $o['id'] ?>)">Selesai</button>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-receipt fs-1 text-muted"></i>
                <p class="text-muted mt-2">Belum ada pesanan</p>
            </div>
        <?php endif ?>
    </div>

    <?php if (isset($pager)): ?>
        <div class="d-flex justify-content-center"><?= $pager->links('default', 'default_full') ?></div>
    <?php endif ?>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
$('#orderTabs .nav-link').on('click', function(e) {
    e.preventDefault();
    $('#orderTabs .nav-link').removeClass('active');
    $(this).addClass('active');
    let status = $(this).data('status');
    if (status === 'all') { $('.order-item').show(); }
    else { $('.order-item').hide(); $('.order-item[data-status="' + status + '"]').show(); }
});

function cancelOrder(id) {
    Swal.fire({
        title: 'Batalkan pesanan?', icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Ya, batalkan', cancelButtonText: 'Tidak'
    }).then(result => {
        if (result.isConfirmed) {
            $.post('<?= base_url('order/cancel') ?>', { id: id }, function(res) {
                if (res.status) { showToast('Pesanan dibatalkan', 'success'); location.reload(); }
                else showToast(res.message, 'danger');
            });
        }
    });
}

function completeOrder(id) {
    Swal.fire({
        title: 'Konfirmasi pesanan selesai?', icon: 'question', showCancelButton: true,
        confirmButtonText: 'Ya, selesai', cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            $.post('<?= base_url('order/complete') ?>', { id: id }, function(res) {
                if (res.status) { showToast('Pesanan selesai!', 'success'); location.reload(); }
                else showToast(res.message, 'danger');
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
