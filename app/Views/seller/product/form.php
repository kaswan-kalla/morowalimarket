<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?><?= isset($product) ? 'Edit' : 'Tambah' ?> Produk<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('seller/products') ?>">Produk</a></li>
                <li class="breadcrumb-item active"><?= isset($product) ? 'Edit' : 'Tambah' ?></li>
            </ol>
        </nav>

        <form id="productForm" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <?php if (isset($product)): ?>
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <?php endif ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Informasi Produk</h6>
                            <div class="mb-3">
                                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required
                                       value="<?= esc($product['name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="5"><?= esc($product['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Foto Produk (Max 5)</h6>
                            <div class="row g-2 mb-3" id="imageGallery">
                                <?php if (isset($images) && !empty($images)): ?>
                                    <?php foreach ($images as $img): ?>
                                        <div class="col-4 col-md-3" id="img-<?= $img['id'] ?>">
                                            <div class="position-relative">
                                                <img src="<?= base_url('uploads/products/' . $img['image']) ?>"
                                                     class="img-fluid rounded border <?= $img['is_main'] ? 'border-primary border-3' : '' ?>">
                                                <?php if (!$img['is_main']): ?>
                                                    <button type="button" class="btn btn-sm btn-primary position-absolute top-0 start-0 m-1"
                                                            onclick="setMainImage(<?= $img['id'] ?>)" title="Jadikan utama">
                                                        <i class="bi bi-star"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge bg-primary position-absolute top-0 start-0 m-1">Utama</span>
                                                <?php endif ?>
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                                        onclick="deleteImage(<?= $img['id'] ?>)">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </div>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*" id="imageInput">
                            <small class="text-muted">Upload max 5 gambar. Format: JPG, PNG, WEBP. Max 2MB per file</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Detail Produk</h6>
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= (isset($product) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                            <?= esc($cat['name']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Harga <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" required min="0"
                                       value="<?= esc($product['price'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Harga Diskon</label>
                                <input type="number" name="discount_price" class="form-control" min="0"
                                       value="<?= esc($product['discount_price'] ?? '0') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" required min="0"
                                       value="<?= esc($product['stock'] ?? '0') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Berat (gram)</label>
                                <input type="number" name="weight" class="form-control" min="0"
                                       value="<?= esc($product['weight'] ?? '0') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">SKU</label>
                                <input type="text" name="sku" class="form-control"
                                       value="<?= esc($product['sku'] ?? '') ?>">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1"
                                       <?= (!isset($product) || $product['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isActive">Produk Aktif</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" id="btnSave">
                                <i class="bi bi-save"></i> <?= isset($product) ? 'Update' : 'Simpan' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
$('#productForm').on('submit', function(e) {
    e.preventDefault();
    $('#btnSave').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
    let url = '<?= isset($product) ? base_url('seller/products/update') : base_url('seller/products/store') ?>';
    $.ajax({
        url: url,
        method: 'POST',
        data: new FormData(this),
        processData: false, contentType: false,
        success: function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                setTimeout(() => window.location.href = '<?= base_url('seller/products') ?>', 1000);
            } else {
                showToast(res.message, 'danger');
                $('#btnSave').prop('disabled', false).html('<i class="bi bi-save"></i> Simpan');
            }
        },
        error: function() {
            showToast('Terjadi kesalahan', 'danger');
            $('#btnSave').prop('disabled', false).html('<i class="bi bi-save"></i> Simpan');
        }
    });
});

function setMainImage(id) {
    $.post('<?= base_url('seller/products/set-main-image') ?>', { image_id: id, product_id: '<?= $product['id'] ?? '' ?>' }, function(res) {
        if (res.status) { showToast('Gambar utama diperbarui', 'success'); location.reload(); }
        else showToast(res.message, 'danger');
    });
}

function deleteImage(id) {
    Swal.fire({
        title: 'Hapus gambar?', icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Ya', cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            $.post('<?= base_url('seller/products/delete-image') ?>', { image_id: id }, function(res) {
                if (res.status) { $('#img-' + id).fadeOut(300, function(){ $(this).remove(); }); showToast('Gambar dihapus', 'success'); }
                else showToast(res.message, 'danger');
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
