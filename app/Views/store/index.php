<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?><?= esc($store['name']) ?><?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>

<!-- Store Banner -->
<div class="bg-primary text-white py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="d-flex align-items-center gap-4">
            <img src="<?= base_url('uploads/stores/' . ($store['logo'] ?? 'default.png')) ?>"
                 class="rounded-circle border border-3 border-white" width="100" height="100" style="object-fit:cover">
            <div>
                <h2 class="fw-bold mb-1"><?= esc($store['name']) ?></h2>
                <p class="mb-0 opacity-75"><?= esc($store['description'] ?? 'Toko Online') ?></p>
                <p class="mb-0 mt-1 small opacity-75">
                    <i class="bi bi-geo-alt"></i> <?= esc($store['city'] ?? '-') ?>
                    <?php if ($store['is_open']): ?>
                        <span class="badge bg-success ms-2">Buka</span>
                    <?php else: ?>
                        <span class="badge bg-danger ms-2">Tutup</span>
                    <?php endif ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container my-4">
    <h5 class="mb-3">Produk <?= esc($store['name']) ?></h5>

    <div class="row">
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
                            <div class="mt-1">
                                <small class="text-muted"><?= $p['sold'] ?> terjual</small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-shop fs-1 text-muted"></i>
                <p class="text-muted mt-2">Toko belum memiliki produk</p>
            </div>
        <?php endif ?>
    </div>

    <?php if (isset($pager)): ?>
        <div class="d-flex justify-content-center mt-3"><?= $pager->links('default', 'default_full') ?></div>
    <?php endif ?>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>
