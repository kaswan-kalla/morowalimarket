<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Profil Saya<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Profile Photo -->
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="<?= base_url('uploads/users/' . ($user['photo'] ?? 'default.png')) ?>"
                             class="rounded-circle" width="120" height="120" style="object-fit:cover" id="profilePreview">
                        <label for="photoInput" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle" style="width:35px;height:35px">
                            <i class="bi bi-camera"></i>
                        </label>
                    </div>
                    <form id="photoForm" enctype="multipart/form-data">
                        <input type="file" name="photo" id="photoInput" accept="image/*" style="display:none">
                    </form>
                    <h5 class="fw-bold mb-0"><?= esc($user['name']) ?></h5>
                    <p class="text-muted mb-0"><?= esc($user['email']) ?></p>
                    <span class="badge bg-info mt-1"><?= ucfirst($user['role']) ?></span>
                </div>
            </div>

            <!-- Edit Profile -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Edit Profil</h6>
                    <form id="profileForm">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="<?= esc($user['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" class="form-control" value="<?= esc($user['phone'] ?? '') ?>">
                        </div>
                        <button type="submit" class="btn btn-primary" id="btnSaveProfile">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Ubah Password</h6>
                    <form id="passwordForm">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="old_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="new_password" class="form-control" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-warning" id="btnChangePassword">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
// Upload photo on file select
$('#photoInput').on('change', function() {
    let formData = new FormData($('#photoForm')[0]);
    formData.append('photo', this.files[0]);
    $.ajax({
        url: '<?= base_url('profile/upload-photo') ?>',
        method: 'POST',
        data: formData,
        processData: false, contentType: false,
        success: function(res) {
            if (res.status) {
                showToast('Foto diperbarui', 'success');
                if (res.data.photo) $('#profilePreview').attr('src', base_url + 'uploads/users/' + res.data.photo);
            } else showToast(res.message, 'danger');
        }
    });
});

// Save profile
$('#profileForm').on('submit', function(e) {
    e.preventDefault();
    $.post('<?= base_url('profile/update') ?>', $(this).serialize(), function(res) {
        if (res.status) showToast('Profil diperbarui', 'success');
        else showToast(res.message, 'danger');
    });
});

// Change password
$('#passwordForm').on('submit', function(e) {
    e.preventDefault();
    $.post('<?= base_url('profile/change-password') ?>', $(this).serialize(), function(res) {
        if (res.status) { showToast('Password diubah', 'success'); $('#passwordForm')[0].reset(); }
        else showToast(res.message, 'danger');
    });
});
</script>
<?= $this->endSection() ?>
