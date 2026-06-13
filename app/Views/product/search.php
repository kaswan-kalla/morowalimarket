<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
            <li class="breadcrumb-item active">Pencarian</li>
        </ol>
    </nav>

    <h4 class="mb-3">Hasil Pencarian: "<?= esc($search_query ?? '') ?>"</h4>

    <div class="row">
        <!-- Filter Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Filter</h6>
                    <form id="filterForm">
                        <input type="hidden" name="q" value="<?= esc($search_query ?? '') ?>">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="category_id" class="form-select form-select-sm">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Harga</label>
                            <div class="d-flex gap-2">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min">
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kota</label>
                            <input type="text" name="city" class="form-control form-control-sm" placeholder="Kota...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Urutkan</label>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="newest">Terbaru</option>
                                <option value="cheapest">Termurah</option>
                                <option value="expensive">Termahal</option>
                                <option value="popular">Terlaris</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Terapkan</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="row" id="productGrid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $p): ?>
                        <div class="col-6 col-md-4 col-lg-3 mb-3">
                            <div class="card h-100 shadow-sm border-0 product-card">
                                <a href="<?= base_url('produk/' . $p['slug']) ?>">
                                    <img src="<?= base_url('uploads/products/' . ($p['main_image'] ?? 'default.png')) ?>"
                                        class="card-img-top lazy" alt="<?= esc($p['name']) ?>" loading="lazy">
                                </a>
                                <?php if ($p['discount_price'] > 0): ?>
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                        -<?= round((($p['price'] - $p['discount_price']) / $p['price']) * 100) ?>%
                                    </span>
                                <?php endif ?>
                                <div class="card-body p-2">
                                    <h6 class="card-title small mb-1">
                                        <a href="<?= base_url('produk/' . $p['slug']) ?>" class="text-dark text-decoration-none">
                                            <?= esc(mb_strimwidth($p['name'], 0, 40, '...')) ?>
                                        </a>
                                    </h6>
                                    <p class="text-danger fw-bold mb-0 small">
                                        Rp <?= number_format($p['discount_price'] > 0 ? $p['discount_price'] : $p['price'], 0, ',', '.') ?>
                                    </p>
                                    <?php if ($p['discount_price'] > 0): ?>
                                        <small class="text-muted text-decoration-line-through">
                                            Rp <?= number_format($p['price'], 0, ',', '.') ?>
                                        </small>
                                    <?php endif ?>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted"><?= esc($p['store_name'] ?? 'Toko') ?></small>
                                        <small class="text-muted"><?= $p['sold'] ?> terjual</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-search fs-1 text-muted"></i>
                        <p class="text-muted mt-3">Produk tidak ditemukan</p>
                    </div>
                <?php endif ?>
            </div>
            <!-- Pagination -->
            <?php if (isset($pager)): ?>
                <div class="d-flex justify-content-center mt-4"><?= $pager->links('default', 'default_full') ?></div>
            <?php endif ?>
        </div>
    </div>
</div>