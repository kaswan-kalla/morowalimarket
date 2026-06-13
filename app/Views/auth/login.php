<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4">
                <div class="card-body">
                    <h3 class="text-center mb-4"><i class="bi bi-shop"></i> Login</h3>

                    <!-- Flash message -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <form id="loginForm">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" required id="loginPassword">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('loginPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="<?= base_url('forgot-password') ?>" class="text-decoration-none">Lupa Password?</a>
                    </div>
                    <hr>
                    <p class="text-center mb-0">
                        Belum punya akun? <a href="<?= base_url('register') ?>" class="text-decoration-none">Daftar</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>