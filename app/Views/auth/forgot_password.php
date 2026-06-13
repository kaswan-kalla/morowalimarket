<?= $this->include('layouts/header') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4">
                <div class="card-body">
                    <h3 class="text-center mb-4"><i class="bi bi-key"></i> Lupa Password</h3>
                    <p class="text-muted text-center">Masukkan email Anda untuk menerima link reset password.</p>

                    <form id="forgotForm">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Kirim Link Reset</button>
                    </form>

                    <hr>
                    <p class="text-center mb-0">
                        <a href="<?= base_url('login') ?>" class="text-decoration-none">Kembali ke Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<script>
$('#forgotForm').on('submit', function(e) {
    e.preventDefault();
    $.post('<?= base_url('forgot-password') ?>', $(this).serialize(), function(res) {
        showToast(res.message, res.status ? 'success' : 'error');
    });
});
</script>
