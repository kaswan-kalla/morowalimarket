<?= $this->include('layouts/header') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4">
                <div class="card-body">
                    <h3 class="text-center mb-4"><i class="bi bi-person-plus"></i> Daftar Akun</h3>

                    <form id="registerForm">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required minlength="3">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirm" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-person-plus me-1"></i> Daftar
                        </button>
                    </form>

                    <hr>
                    <p class="text-center mb-0">
                        Sudah punya akun? <a href="<?= base_url('login') ?>" class="text-decoration-none">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<script>
$('#registerForm').on('submit', function(e) {
    e.preventDefault();
    $.post('<?= base_url('register') ?>', $(this).serialize(), function(res) {
        if (res.status) {
            showToast(res.message, 'success');
            setTimeout(() => window.location.href = res.redirect || '/login', 1500);
        } else {
            showToast(res.message, 'error');
        }
    });
});
</script>
