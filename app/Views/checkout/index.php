<div class="container my-4">
    <h4 class="mb-4"><i class="bi bi-bag-check"></i> Checkout</h4>

    <form id="checkoutForm">
        <?= csrf_field() ?>
        <div class="row">
            <div class="col-lg-8">
                <!-- Alamat -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Alamat Pengiriman</h6>
                        <div id="addressList">
                            <?php if (!empty($addresses)): ?>
                                <?php foreach ($addresses as $addr): ?>
                                    <div class="form-check border rounded p-3 mb-2">
                                        <input class="form-check-input" type="radio" name="address_id" value="<?= $addr['id'] ?>"
                                            <?= ($addr['is_default']) ? 'checked' : '' ?>>
                                        <label class="form-check-label w-100">
                                            <strong><?= esc($addr['label']) ?></strong><br>
                                            <small><?= esc($addr['recipient_name']) ?> - <?= esc($addr['phone']) ?></small><br>
                                            <small class="text-muted"><?= esc($addr['address']) ?>, <?= esc($addr['city']) ?> <?= esc($addr['postal_code']) ?></small>
                                        </label>
                                    </div>
                                <?php endforeach ?>
                            <?php else: ?>
                                <p class="text-muted">Belum ada alamat. <a href="<?= base_url('address') ?>">Tambah alamat</a></p>
                            <?php endif ?>
                        </div>
                    </div>
                </div>

                <!-- Kurir -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Jasa Pengiriman</h6>
                        <select name="courier" class="form-select" required>
                            <option value="">Pilih Kurir</option>
                            <option value="jne">JNE</option>
                            <option value="jnt">J&T Express</option>
                            <option value="sicepat">SiCepat</option>
                            <option value="anteraja">Anteraja</option>
                            <option value="pos">POS Indonesia</option>
                        </select>
                    </div>
                </div>

                <!-- Voucher -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Kode Voucher</h6>
                        <div class="input-group">
                            <input type="text" name="voucher_code" class="form-control" placeholder="Masukkan kode voucher" id="voucherInput">
                            <button type="button" class="btn btn-outline-primary" id="btnApplyVoucher">Terapkan</button>
                        </div>
                        <div id="voucherResult" class="mt-2"></div>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Catatan</h6>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan untuk penjual (opsional)"></textarea>
                    </div>
                </div>

                <!-- Items -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Produk Dipesan</h6>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="d-flex align-items-center gap-3 border-bottom pb-2 mb-2">
                                <img src="<?= base_url('uploads/products/' . ($item['image'] ?? 'default.png')) ?>"
                                    class="rounded" width="60" height="60" style="object-fit:cover">
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-semibold small"><?= esc($item['product_name']) ?></p>
                                    <small class="text-muted"><?= $item['qty'] ?> x Rp <?= number_format($item['discount_price'] > 0 ? $item['discount_price'] : $item['price'], 0, ',', '.') ?></small>
                                </div>
                                <span class="fw-bold text-danger">
                                    Rp <?= number_format(($item['discount_price'] > 0 ? $item['discount_price'] : $item['price']) * $item['qty'], 0, ',', '.') ?>
                                </span>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top:80px">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Ringkasan Pesanan</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal (<?= count($cart_items) ?> produk)</span>
                            <span id="subtotal">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkos Kirim</span>
                            <span id="shippingCost">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success" id="discountRow" style="display:none">
                            <span>Diskon Voucher</span>
                            <span id="discountAmount">- Rp 0</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total Pembayaran</span>
                            <span class="fw-bold text-danger fs-5" id="grandTotal">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg" id="btnPlaceOrder">
                            <i class="bi bi-bag-check"></i> Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>