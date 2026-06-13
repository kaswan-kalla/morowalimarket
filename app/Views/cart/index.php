<div class="container my-4">
    <h4 class="mb-4"><i class="bi bi-cart3"></i> Keranjang Belanja</h4>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div id="cartItems">
                        <!-- Items loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top:80px">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Ringkasan</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Produk</span>
                        <span id="totalItems">0 item</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total Harga</span>
                        <span class="fw-bold text-danger" id="totalPrice">Rp 0</span>
                    </div>
                    <hr>
                    <a href="<?= base_url('checkout') ?>" class="btn btn-primary w-100" id="btnCheckout">
                        <i class="bi bi-bag-check"></i> Checkout
                    </a>
                    <a href="<?= base_url('produk') ?>" class="btn btn-outline-secondary w-100 mt-2">Lanjut Belanja</a>
                </div>
            </div>
        </div>
    </div>
</div>