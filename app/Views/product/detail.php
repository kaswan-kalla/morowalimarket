<div class="container py-4">
    <div class="row g-4">
        <!-- Gallery -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <!-- Main Image -->
                    <div class="mb-3 text-center">
                        <?php $mainImg = $product['main_image'] ?? null; ?>
                        <img id="mainImage" src="<?= $mainImg ? base_url($mainImg) : 'https://via.placeholder.com/500x500?text=No+Image' ?>"
                            alt="<?= esc($product['name']) ?>" class="img-fluid rounded" style="max-height:400px;object-fit:contain;">
                    </div>
                    <!-- Thumbnails -->
                    <?php if (!empty($images)): ?>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php foreach ($images as $img): ?>
                                <img src="<?= base_url($img['image']) ?>" alt="Thumb"
                                    class="rounded border" style="width:70px;height:70px;object-fit:cover;cursor:pointer;"
                                    onclick="$('#mainImage').attr('src', '<?= base_url($img['image']) ?>')">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-md-6">
            <h2 class="fw-bold"><?= esc($product['name']) ?></h2>

            <!-- Rating -->
            <div class="mb-2">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="bi bi-star<?= $i <= $rating['avg_rating'] ? '-fill text-warning' : '' ?>"></i>
                <?php endfor; ?>
                <span class="text-muted ms-2"><?= $rating['avg_rating'] ?> (<?= $rating['total_reviews'] ?> ulasan)</span>
                <span class="text-muted ms-3"><i class="bi bi-bag-check me-1"></i>Terjual <?= $product['sold'] ?></span>
            </div>

            <!-- Price -->
            <div class="card bg-light mb-3">
                <div class="card-body py-3">
                    <?php $price = $product['discount_price'] > 0 ? $product['discount_price'] : $product['price']; ?>
                    <?php if ($product['discount_price'] > 0): ?>
                        <span class="price-original fs-5"><?= format_rupiah($product['price']) ?></span>
                        <span class="badge bg-danger ms-2">-<?= round((1 - $product['discount_price'] / $product['price']) * 100) ?>%</span>
                        <br>
                    <?php endif; ?>
                    <span class="price-current fs-3"><?= format_rupiah($price) ?></span>
                </div>
            </div>

            <!-- Store Info -->
            <div class="d-flex align-items-center mb-3 p-3 border rounded">
                <i class="bi bi-shop fs-4 text-primary me-3"></i>
                <div>
                    <a href="<?= base_url('toko/' . $product['store_slug']) ?>" class="text-decoration-none fw-semibold"><?= esc($product['store_name']) ?></a>
                    <br><small class="text-muted"><?= esc($product['category_name'] ?? 'Tanpa Kategori') ?></small>
                </div>
            </div>

            <!-- SKU & Stock -->
            <div class="row mb-3">
                <div class="col-6">
                    <small class="text-muted">SKU</small><br>
                    <strong><?= esc($product['sku'] ?? '-') ?></strong>
                </div>
                <div class="col-6">
                    <small class="text-muted">Stok</small><br>
                    <strong class="<?= $product['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                        <?= $product['stock'] > 0 ? $product['stock'] . ' tersedia' : 'Habis' ?>
                    </strong>
                </div>
            </div>

            <!-- Quantity & Actions -->
            <?php if ($product['stock'] > 0): ?>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <label class="fw-semibold">Jumlah:</label>
                    <div class="input-group" style="width:140px;">
                        <button class="btn btn-outline-secondary" onclick="changeQty(-1)">-</button>
                        <input type="number" id="qty" class="form-control text-center" value="1" min="1" max="<?= $product['stock'] ?>">
                        <button class="btn btn-outline-secondary" onclick="changeQty(1)">+</button>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" onclick="addToCart(<?= $product['id'] ?>)">
                        <i class="bi bi-cart-plus me-2"></i>Tambah ke Keranjang
                    </button>
                    <button class="btn btn-outline-danger" onclick="buyNow(<?= $product['id'] ?>)">
                        <i class="bi bi-lightning me-2"></i>Beli Sekarang
                    </button>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i>Produk sedang habis
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Description -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="fw-bold">Deskripsi Produk</h5>
            <div class="mt-2"><?= nl2br(esc($product['description'] ?? 'Belum ada deskripsi')) ?></div>
        </div>
    </div>

    <!-- Reviews -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Ulasan Pembeli</h5>
            <div id="reviewsContainer">
                <?php if (!empty($reviews['reviews'])): ?>
                    <?php foreach ($reviews['reviews'] as $r): ?>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <strong><?= esc($r['user_name']) ?></strong>
                                <span class="ms-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star<?= $i <= $r['rating'] ? '-fill text-warning' : '' ?> small"></i>
                                    <?php endfor; ?>
                                </span>
                                <small class="text-muted ms-auto"><?= date('d M Y', strtotime($r['created_at'])) ?></small>
                            </div>
                            <p class="mb-0"><?= esc($r['comment'] ?? '') ?></p>
                            <?php if ($r['photo']): ?>
                                <img src="<?= base_url($r['photo']) ?>" class="rounded mt-2" style="max-height:100px;">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Belum ada ulasan</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related)): ?>
        <h5 class="fw-bold mt-4 mb-3">Produk Serupa</h5>
        <div class="row g-3">
            <?php foreach ($related as $r): ?>
                <?php $rPrice = $r['discount_price'] > 0 ? $r['discount_price'] : $r['price']; ?>
                <div class="col-6 col-md-3">
                    <div class="card product-card h-100">
                        <a href="<?= base_url('produk/' . $r['slug']) ?>" class="text-decoration-none">
                            <?php if ($r['main_image']): ?>
                                <img src="<?= base_url($r['main_image']) ?>" class="card-img-top" loading="lazy" style="height:200px;object-fit:cover;">
                            <?php else: ?>
                                <div class="img-placeholder card-img-top" style="height:200px;"><i class="bi bi-image"></i></div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h6 class="card-title text-dark" style="font-size:0.85rem;"><?= esc($r['name']) ?></h6>
                                <div class="price-current"><?= format_rupiah($rPrice) ?></div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>