<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Pengaturan Toko<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <h4 class="mb-4">Pengaturan Toko</h4>

        <form id="storeForm" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <?php if (isset($store)): ?>
                <input type="hidden" name="id" value="<?= $store['id'] ?>">
            <?php endif ?>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Informasi Toko</h6>
                            <div class="text-center mb-4">
                                <img src="<?= base_url('uploads/stores/' . ($store['logo'] ?? 'default.png')) ?>"
                                     class="rounded-circle border" width="100" height="100" style="object-fit:cover" id="logoPreview">
                                <div class="mt-2">
                                    <label for="logoInput" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-camera"></i> Ganti Logo
                                    </label>
                                    <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required
                                       value="<?= esc($store['name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="3"><?= esc($store['description'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kota</label>
                                <input type="text" name="city" class="form-control" value="<?= esc($store['city'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat Toko</label>
                                <textarea name="address" class="form-control" rows="2"><?= esc($store['address'] ?? '') ?></textarea>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_open" class="form-check-input" id="isOpen" value="1"
                                       <?= (!isset($store) || $store['is_open']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isOpen">Toko Buka</label>
                            </div>

                            <button type="submit" class="btn btn-primary" id="btnSave">
                                <i class="bi bi-save"></i> <?= isset($store) ? 'Update Toko' : 'Buka Toko' ?>
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
$('#logoInput').on('change', function() {
    if (this.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) { $('#logoPreview').attr('src', e.target.result); };
        reader.readAsDataURL(this.files[0]);
    }
});

$('#storeForm').on('submit', function(e) {
    e.preventDefault();
    $('#btnSave').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
    let url = '<?= isset($store) ? base_url('seller/store/update') : base_url('seller/store/create') ?>';
    $.ajax({
        url: url,
        method: 'POST',
        data: new FormData(this),
        processData: false, contentType: false,
        success: function(res) {
            if (res.status) { showToast(res.message, 'success'); setTimeout(() => location.reload(), 1000); }
            else { showToast(res.message, 'danger'); $('#btnSave').prop('disabled', false).html('<i class="bi bi-save"></i> Simpan'); }
        }
    });
});
</script>
<?= $this->endSection() ?>
