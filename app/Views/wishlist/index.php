<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Wishlist Saya<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>

<div class="container my-4">
    <h4 class="mb-4"><i class="bi bi-heart"></i> Wishlist Saya</h4>

    <div class="row" id="wishlistGrid">
        <?php if (!empty($wishlist)): ?>
            <?php foreach ($wishlist as $w): ?>
                <div class="col-6 col-md-4 col-lg-3 mb-3" id="wishlist-<?= $w['id'] ?>">
                    <div class="card h-100 shadow-sm border-0 product-card">
                        <a href="<?= base_url('produk/' . $w['slug']) ?>">
                            <img src="<?= base_url('uploads/products/' . ($w['main_image'] ?? 'default.png')) ?>"
                                 class="card-img-top lazy" alt="<?= esc($w['name']) ?>" loading="lazy">
                        </a>
                        <div class="card-body p-2">
                            <h6 class="card-title small mb-1">
                                <a href="<?= base_url('produk/' . $w['slug']) ?>" class="text-dark text-decoration-none">
                                    <?= esc(mb_strimwidth($w['name'], 0, 40, '...')) ?>
                                </a>
                            </h6>
                            <p class="text-danger fw-bold mb-1 small">
                                Rp <?= number_format($w['discount_price'] > 0 ? $w['discount_price'] : $w['price'], 0, ',', '.') ?>
                            </p>
                            <button class="btn btn-sm btn-outline-danger w-100" onclick="removeWishlist(<?= $w['wishlist_id'] ?>)">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-heart fs-1 text-muted"></i>
                <p class="text-muted mt-2">Wishlist kosong</p>
                <a href="<?= base_url('produk') ?>" class="btn btn-primary">Mulai Belanja</a>
            </div>
        <?php endif ?>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
function removeWishlist(id) {
    $.post('<?= base_url('wishlist/toggle') ?>', { product_id: id }, function(res) {
        if (res.status) {
            $('#wishlist-' + id).fadeOut(300, function() { $(this).remove(); });
            showToast('Dihapus dari wishlist', 'success');
        } else {
            showToast(res.message, 'danger');
        }
    });
}
</script>
<?= $this->endSection() ?>
