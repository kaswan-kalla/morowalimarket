<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Detail Pesanan <?= esc($order['order_number']) ?><?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('seller/orders') ?>">Pesanan</a></li>
                <li class="breadcrumb-item active"><?= esc($order['order_number']) ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0"><?= esc($order['order_number']) ?></h5>
                            <span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?> px-3 py-2">
                                <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                            </span>
                        </div>

                        <h6 class="fw-bold">Produk Dipesan</h6>
                        <?php foreach ($items as $item): ?>
                            <div class="d-flex align-items-center gap-3 border-bottom pb-2 mb-2">
                                <img src="<?= base_url('uploads/products/' . ($item['image'] ?? 'default.png')) ?>"
                                     class="rounded" width="60" height="60" style="object-fit:cover">
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-semibold"><?= esc($item['product_name']) ?></p>
                                    <small class="text-muted"><?= $item['qty'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></small>
                                </div>
                                <span class="fw-bold text-danger">Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></span>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Pengiriman</h6>
                        <p class="mb-1"><strong>Penerima:</strong> <?= esc($order['recipient_name'] ?? '-') ?></p>
                        <p class="mb-1"><strong>Alamat:</strong> <?= esc($order['shipping_address'] ?? '-') ?></p>
                        <p class="mb-1"><strong>Kurir:</strong> <?= strtoupper(esc($order['courier'] ?? '-')) ?></p>
                        <?php if (!empty($order['tracking_number'])): ?>
                            <p class="mb-0"><strong>No. Resi:</strong> <?= esc($order['tracking_number']) ?></p>
                        <?php endif ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Ringkasan</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span><span>Rp <?= number_format($order['subtotal'], 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkir</span><span>Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></span>
                        </div>
                        <?php if ($order['discount_amount'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Diskon</span><span>- Rp <?= number_format($order['discount_amount'], 0, ',', '.') ?></span>
                            </div>
                        <?php endif ?>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold text-danger">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Aksi</h6>
                        <?php if ($order['status'] == 'awaiting_payment'): ?>
                            <button class="btn btn-warning w-100 mb-2" onclick="processOrder()">Proses Pesanan</button>
                        <?php endif ?>
                        <?php if ($order['status'] == 'processing'): ?>
                            <button class="btn btn-success w-100 mb-2" onclick="shipOrder()">Kirim Pesanan</button>
                        <?php endif ?>
                        <p class="text-muted small mb-0">Tanggal: <?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
function processOrder() {
    $.post('<?= base_url('seller/orders/process') ?>', { id: <?= $order['id'] ?> }, function(res) {
        if (res.status) { showToast('Pesanan diproses', 'success'); location.reload(); }
        else showToast(res.message, 'danger');
    });
}

function shipOrder() {
    Swal.fire({
        title: 'Input No. Resi', input: 'text', inputPlaceholder: 'Masukkan nomor resi',
        showCancelButton: true, confirmButtonText: 'Kirim', cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed && result.value) {
            $.post('<?= base_url('seller/orders/ship') ?>', { id: <?= $order['id'] ?>, tracking_number: result.value }, function(res) {
                if (res.status) { showToast('Pesanan dikirim', 'success'); location.reload(); }
                else showToast(res.message, 'danger');
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
