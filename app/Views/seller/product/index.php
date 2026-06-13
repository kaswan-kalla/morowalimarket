<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Produk Saya<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Produk Saya</h4>
            <a href="<?= base_url('seller/products/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus"></i> Tambah Produk
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Gambar</th>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Terjual</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td>
                                            <img src="<?= base_url('uploads/products/' . ($p['main_image'] ?? 'default.png')) ?>"
                                                 class="rounded" width="50" height="50" style="object-fit:cover">
                                        </td>
                                        <td>
                                            <a href="<?= base_url('produk/' . $p['slug']) ?>" class="text-decoration-none fw-semibold">
                                                <?= esc(mb_strimwidth($p['name'], 0, 30, '...')) ?>
                                            </a>
                                            <br><small class="text-muted">SKU: <?= esc($p['sku'] ?? '-') ?></small>
                                        </td>
                                        <td><?= esc($p['category_name'] ?? '-') ?></td>
                                        <td>
                                            <span class="text-danger fw-bold">Rp <?= number_format($p['discount_price'] > 0 ? $p['discount_price'] : $p['price'], 0, ',', '.') ?></span>
                                            <?php if ($p['discount_price'] > 0): ?>
                                                <br><small class="text-decoration-line-through text-muted">Rp <?= number_format($p['price'], 0, ',', '.') ?></small>
                                            <?php endif ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $p['stock'] <= 5 ? 'danger' : ($p['stock'] <= 20 ? 'warning' : 'success') ?>">
                                                <?= $p['stock'] ?>
                                            </span>
                                        </td>
                                        <td><?= $p['sold'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= $p['is_active'] ? 'success' : 'secondary' ?>">
                                                <?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('seller/products/edit/' . $p['id']) ?>" class="btn btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-outline-danger" onclick="deleteProduct(<?= $p['id'] ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada produk</td></tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pager)): ?>
                    <div class="d-flex justify-content-center"><?= $pager->links('default', 'default_full') ?></div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
function deleteProduct(id) {
    Swal.fire({
        title: 'Hapus produk?', icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            $.post('<?= base_url('seller/products/delete') ?>', { id: id }, function(res) {
                if (res.status) { showToast('Produk dihapus', 'success'); location.reload(); }
                else showToast(res.message, 'danger');
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
