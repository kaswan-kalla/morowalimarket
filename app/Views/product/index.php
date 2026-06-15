<?php

/** @var string $meta_title */
/** @var array $products */
/** @var int $total */
/** @var int $limit */
/** @var int $page */
?>
<div class="container py-4">
    <h4 class="fw-bold mb-4"><?= esc($meta_title ?? 'Semua Produk') ?></h4>

    <!-- Filter & Sort -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select class="form-select" id="sortSelect">
                        <option value="newest">Terbaru</option>
                        <option value="cheapest">Termurah</option>
                        <option value="expensive">Termahal</option>
                        <option value="popular">Terlaris</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" id="minPrice" placeholder="Harga min">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" id="maxPrice" placeholder="Harga max">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" onclick="applyFilter()">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row g-3" id="productGrid">
        <?php foreach ($products as $p): ?>
            <?php $price = $p['discount_price'] ?: $p['price']; ?>
            <div class="col-6 col-md-3">
                <div class="card product-card position-relative h-100">
                    <?php if ($p['discount_price']): ?>
                        <span class="badge-discount">-<?= round((1 - $p['discount_price'] / $p['price']) * 100) ?>%</span>
                    <?php endif; ?>
                    <a href="<?= base_url('produk/' . $p['slug']) ?>" class="text-decoration-none">
                        <?php if ($p['main_image']): ?>
                            <img src="<?= base_url($p['main_image']) ?>" alt="<?= esc($p['name']) ?>" class="card-img-top" loading="lazy">
                        <?php else: ?>
                            <div class="img-placeholder card-img-top" style="height:200px;"><i class="bi bi-image"></i></div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h6 class="card-title text-dark" style="font-size:0.9rem;"><?= esc($p['name']) ?></h6>
                            <div class="price-current"><?= format_rupiah($price) ?></div>
                            <?php if ($p['discount_price']): ?>
                                <div class="price-original"><?= format_rupiah($p['price']) ?></div>
                            <?php endif; ?>
                            <small class="text-muted d-block"><i class="bi bi-shop me-1"></i><?= esc($p['store_name']) ?></small>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= esc($p['store_city'] ?? '-') ?></small>
                        </div>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
        <div class="text-center py-5">
            <i class="bi bi-box fs-1 text-muted"></i>
            <p class="text-muted">Belum ada produk tersedia</p>
        </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($total > $limit): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= ceil($total / $limit); $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&sort=<?= $this->request->getGet('sort') ?? 'newest' ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>