<?= $this->include('layouts/header') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4">
                <div class="card-body">
                    <h3 class="text-center mb-4">Reset Password</h3>
                    <form id="resetForm">
                        <input type="hidden" name="token" value="<?= esc($token) ?>">
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<script>
$('#resetForm').on('submit', function(e) {
    e.preventDefault();
    $.post('<?= base_url('reset-password') ?>', $(this).serialize(), function(res) {
        if (res.status) {
            showToast(res.message, 'success');
            setTimeout(() => window.location.href = res.redirect || '/login', 1500);
        } else {
            showToast(res.message, 'error');
        }
    });
});
</script>
