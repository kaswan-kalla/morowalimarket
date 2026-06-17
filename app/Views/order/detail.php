<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('order') ?>">Pesanan</a></li>
            <li class="breadcrumb-item active"><?= esc($order['order_number']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Status -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">No. Pesanan</small>
                            <h5 class="fw-bold mb-0"><?= esc($order['order_number']) ?></h5>
                        </div>
                        <span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?> px-3 py-2 fs-6">
                            <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Produk Dipesan</h6>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
                            <img src="<?= base_url($item['product_image'] ?? 'uploads/products/default.j') ?>"
                                class="rounded" width="80" height="80" style="object-fit:cover">
                            <div class="flex-grow-1">
                                <a href="<?= base_url('produk/' . ($item['product_slug'] ?? '')) ?>" class="text-dark fw-semibold text-decoration-none">
                                    <?= esc($item['product_name']) ?>
                                </a>
                                <br><small class="text-muted"><?= $item['qty'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></small>
                            </div>
                            <span class="fw-bold text-danger">Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></span>
                        </div>
                    <?php endforeach ?>

                    <!-- Review button for completed orders -->
                    <?php if ($order['status'] == 'completed'): ?>
                        <?php foreach ($items as $item): ?>
                            <div class="mb-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="showReviewForm(<?= $item['product_id'] ?>, <?= $order['id'] ?>)">
                                    <i class="bi bi-star"></i> Beri Ulasan <?= esc($item['product_name']) ?>
                                </button>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                </div>
            </div>

            <!-- Shipping -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Informasi Pengiriman</h6>
                    <p class="mb-1"><strong>Penerima:</strong> <?= esc($order['recipient_name'] ?? '-') ?></p>
                    <p class="mb-1"><strong>Telepon:</strong> <?= esc($order['phone'] ?? '-') ?></p>
                    <p class="mb-1"><strong>Alamat:</strong> <?= esc($order['shipping_address'] ?? '-') ?></p>
                    <p class="mb-1"><strong>Kurir:</strong> <?= strtoupper(esc($order['courier'] ?? '-')) ?></p>
                    <?php if (!empty($order['tracking_number'])): ?>
                        <p class="mb-0"><strong>No. Resi:</strong> <span class="text-primary fw-bold"><?= esc($order['tracking_number']) ?></span></p>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Ringkasan Pembayaran</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>Rp <?= number_format($order['subtotal'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ongkos Kirim</span>
                        <span>Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></span>
                    </div>
                    <?php if ($order['discount_amount'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Diskon</span>
                            <span>- Rp <?= number_format($order['discount_amount'], 0, ',', '.') ?></span>
                        </div>
                    <?php endif ?>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold text-danger fs-5">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Info Pesanan</h6>
                    <p class="mb-1"><small class="text-muted">Dibuat:</small><br><?= date('d M Y H:i', strtotime($order['created_at'])) ?> WIB</p>
                    <?php if (!empty($order['paid_at'])): ?>
                        <p class="mb-1"><small class="text-muted">Dibayar:</small><br><?= date('d M Y H:i', strtotime($order['paid_at'])) ?> WIB</p>
                    <?php endif ?>
                    <p class="mb-0"><small class="text-muted">Metode Bayar:</small><br><?= strtoupper(esc($order['payment_method'] ?? '-')) ?></p>
                </div>
            </div>

            <?php
            $payDetails = !empty($order['payment_details']) ? json_decode($order['payment_details'], true) : null;
            ?>
            <?php if (!empty($payDetails)): ?>
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Detail Pembayaran</h6>
                        <?php if (!empty($payDetails['va_number'])): ?>
                            <div class="mb-2">
                                <small class="text-muted">Nomor BRIVA</small>
                                <p class="mb-0 fw-bold text-primary fs-5"><?= esc($payDetails['va_number']) ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($payDetails['qr_url'])): ?>
                            <div class="mb-2 text-center">
                                <small class="text-muted d-block mb-2">Scan QRIS</small>
                                <img src="<?= esc($payDetails['qr_url']) ?>" class="img-fluid" style="max-width:200px" alt="QRIS">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($order['status'] == 'awaiting_payment'): ?>
                <a href="<?= base_url('payment/' . $order['id']) ?>" class="btn btn-primary w-100">Bayar Sekarang</a>
            <?php endif ?>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Beri Ulasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reviewForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="reviewProductId">
                    <input type="hidden" name="order_id" id="reviewOrderId">
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <div class="rating-stars" id="ratingStars">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" required>
                                <label for="star<?= $i ?>"><i class="bi bi-star-fill fs-4 text-warning"></i></label>
                            <?php endfor ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Komentar</label>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Tulis ulasan..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto (opsional)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                </div>
            </form>
        </div>
    </div>
</div>