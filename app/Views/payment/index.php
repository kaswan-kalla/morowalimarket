<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <!-- Pembayaran Berhasil -->
            <?php if (in_array($order['status'], ['processing', 'shipped', 'completed'])): ?>
                <div class="card shadow-sm border-success">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:4rem"></i>
                        <h4 class="mt-3 fw-bold">Pembayaran Berhasil!</h4>
                        <p class="text-muted mb-1">No. Pesanan: <strong><?= esc($order['order_number']) ?></strong></p>
                        <p class="text-muted mb-4">Total: <strong class="text-danger">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></strong></p>
                        <?php if ($order['status'] === 'processing'): ?>
                            <p class="small text-muted">Pesanan sedang diproses oleh penjual. Anda akan diberitahu saat pesanan dikirim.</p>
                        <?php elseif ($order['status'] === 'shipped'): ?>
                            <p class="small text-muted">Pesanan sedang dalam perjalanan.</p>
                        <?php endif; ?>
                        <div class="d-flex gap-2 justify-content-center mt-3">
                            <a href="<?= base_url('order/' . $order['id']) ?>" class="btn btn-primary">
                                <i class="bi bi-receipt"></i> Lihat Pesanan
                            </a>
                            <a href="<?= base_url('produk') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-bag"></i> Belanja Lagi
                            </a>
                        </div>
                    </div>
                </div>

            <?php else: ?>

                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h4 class="mb-3">Pembayaran</h4>
                        <p class="text-muted">No. Pesanan: <strong><?= esc($order['order_number']) ?></strong></p>
                        <h3 class="text-danger fw-bold mb-4">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></h3>
                    </div>
                </div>

                <!-- Midtrans Snap Payment -->
                <?php if (!empty($snapToken) && $order['status'] === 'awaiting_payment'): ?>
                    <div class="card shadow-sm mt-3">
                        <div class="card-body text-center">
                            <h6 class="fw-bold mb-3">Bayar Sekarang</h6>
                            <p class="text-muted small mb-3">Pilih metode pembayaran BRIVA atau QRIS melalui popup Midtrans.</p>
                            <button type="button" class="btn btn-primary w-100 btn-lg" id="btnSnapPay">
                                <i class="bi bi-credit-card"></i> Bayar dengan Midtrans
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Payment Details (BRIVA / QRIS info dari webhook) -->
                <?php if (!empty($paymentDetails)): ?>
                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Detail Pembayaran</h6>
                            <?php if (!empty($paymentDetails['va_number'])): ?>
                                <div class="mb-2">
                                    <small class="text-muted">Metode</small>
                                    <p class="mb-0 fw-semibold">
                                        <i class="bi bi-bank"></i> BRI Virtual Account
                                    </p>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Nomor VA</small>
                                    <p class="mb-0 fw-bold text-primary fs-5"><?= esc($paymentDetails['va_number']) ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($paymentDetails['qr_url'])): ?>
                                <div class="mb-2 text-center">
                                    <small class="text-muted d-block mb-2">Scan QRIS untuk membayar</small>
                                    <img src="<?= esc($paymentDetails['qr_url']) ?>" class="img-fluid" style="max-width:250px" alt="QRIS Code">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Manual Upload (fallback) -->
                <?php if ($order['status'] === 'awaiting_payment' && empty($paymentDetails)): ?>
                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Upload Bukti Pembayaran Manual</h6>
                            <form id="paymentForm" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <input type="hidden" name="payment_method" id="selectedMethod" value="transfer">
                                <div class="mb-3">
                                    <label class="form-label">Bukti Transfer</label>
                                    <input type="file" name="proof" class="form-control" accept="image/*" required>
                                    <small class="text-muted">Format: JPG, PNG, WEBP. Max: 2MB</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Catatan (opsional)</label>
                                    <input type="text" name="notes" class="form-control" placeholder="Nama pengirim, bank, dll">
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="btnUploadProof">
                                    <i class="bi bi-upload"></i> Upload Bukti
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Status pembayaran pending/rejected -->
                <?php if (!empty($payment) && $order['status'] === 'awaiting_payment'): ?>
                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Status Pembayaran</h6>
                            <span class="badge bg-<?= $payment['status'] == 'verified' ? 'success' : ($payment['status'] == 'rejected' ? 'danger' : 'warning') ?> px-3 py-2">
                                <?= ucfirst($payment['status']) ?>
                            </span>
                            <?php if ($payment['status'] == 'rejected' && !empty($payment['notes'])): ?>
                                <p class="text-danger mt-2 mb-0"><small>Catatan: <?= esc($payment['notes']) ?></small></p>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endif ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Hidden data for JS -->
<script>
    var snapToken = '<?= $snapToken ?? '' ?>';
    var orderId = <?= $order['id'] ?? 0 ?>;
</script>