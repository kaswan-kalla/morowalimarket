<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h4 class="mb-3">Pembayaran</h4>
                    <p class="text-muted">No. Pesanan: <strong><?= esc($order['order_number']) ?></strong></p>
                    <h3 class="text-danger fw-bold mb-4">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></h3>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Pilih Metode Pembayaran</h6>
                    <div class="form-check border rounded p-3 mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" value="transfer" id="methodTransfer" checked>
                        <label class="form-check-label" for="methodTransfer">
                            <i class="bi bi-bank"></i> <strong>Transfer Bank</strong>
                            <div class="mt-2 small" id="bankInfo" style="display:none">
                                <p class="mb-1">BCA: <strong>1234567890</strong> (a.n. Marketplace)</p>
                                <p class="mb-1">Mandiri: <strong>0987654321</strong> (a.n. Marketplace)</p>
                            </div>
                        </label>
                    </div>
                    <div class="form-check border rounded p-3 mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" value="qris" id="methodQris">
                        <label class="form-check-label" for="methodQris">
                            <i class="bi bi-qr-code"></i> <strong>QRIS</strong>
                            <div class="mt-2 text-center" id="qrisInfo" style="display:none">
                                <img src="<?= base_url('public/asset/img/qris-placeholder.png') ?>" class="img-fluid" style="max-width:200px" alt="QRIS">
                                <p class="small text-muted mt-1">Scan QR Code di atas</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Upload Proof -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Upload Bukti Pembayaran</h6>
                    <form id="paymentForm" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <input type="hidden" name="payment_method" id="selectedMethod" value="transfer">
                        <div class="mb-3">
                            <label class="form-label">Bukti Transfer</label>
                            <input type="file" name="proof" class="form-control" accept="image/*" required>
                            <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
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

            <?php if (!empty($payment)): ?>
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
        </div>
    </div>
</div>