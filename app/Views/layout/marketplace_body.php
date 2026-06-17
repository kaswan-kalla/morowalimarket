<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $meta_title ?? 'Marketplace - Multi Vendor' ?></title>
    <meta name="description" content="<?= $meta_description ?? 'Marketplace Multi Vendor - Belanja Online Terpercaya' ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="<?= asset_url('asset/pavicon.ico') ?>" type="image/x-icon">

    <style>
        :root {
            --primary: #0d6efd;
            --secondary: #6c757d;
            --success: #198754;
            --warning: #ffc107;
            --danger: #dc3545;
            --bg-light: #f8f9fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 70px;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .product-card img {
            border-radius: 12px 12px 0 0;
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .badge-discount {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--danger);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn-wishlist {
            position: absolute;
            top: 52px;
            right: 10px;
            background: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.2s;
            z-index: 2;
        }

        .btn-cart-add {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.2s;
            z-index: 2;
        }

        .btn-cart-add:hover {
            background: #b02a37;
            transform: scale(1.1);
        }

        .btn-wishlist:hover,
        .btn-wishlist.active {
            color: var(--danger);
        }

        .price-original {
            text-decoration: line-through;
            color: var(--secondary);
            font-size: 0.85rem;
        }

        .price-current {
            font-weight: 700;
            color: var(--danger);
            font-size: 1.1rem;
        }

        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            z-index: 9998;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.show {
            display: flex;
        }

        .img-placeholder {
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 2rem;
        }

        /* Sidebar link hover */
        .sidebar-link {
            color: #333;
            border-radius: 0;
            transition: background-color 0.15s, color 0.15s;
        }

        .sidebar-link:hover {
            background-color: #f0f4ff;
            color: var(--primary);
        }

        .sidebar-link.text-danger:hover {
            background-color: #fff0f0;
            color: #b02a37 !important;
        }

        /* Desktop Sidebar */
        .sidebar-desktop {
            position: fixed;
            top: 70px;
            left: 0;
            width: 260px;
            height: calc(100vh - 70px);
            background: #fff;
            border-right: 1px solid #dee2e6;
            z-index: 1020;
            overflow: hidden;
        }

        .sidebar-desktop .sidebar-link {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        @media (min-width: 992px) {
            body.sidebar-visible #page-content {
                margin-left: 260px;
            }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>

<body class="<?= is_logged_in() ? 'sidebar-visible' : '' ?>">

    <?= $this->include('layouts/navbar') ?>

    <?php if (is_logged_in()): ?>
        <?php $user = get_user(); ?>
        <!-- Desktop Sidebar -->
        <aside class="sidebar-desktop d-none d-lg-flex flex-column">
            <!-- User Info -->
            <div class="sidebar-user px-3 py-3 border-bottom text-center">
                <?php if (!empty($user['photo'])): ?>
                    <img src="<?= base_url($user['photo']) ?>" class="rounded-circle mb-1" width="56" height="56" alt="Avatar" style="object-fit:cover;">
                <?php else: ?>
                    <div class="mb-1"><i class="bi bi-person-circle fs-1"></i></div>
                <?php endif; ?>
                <div class="fw-semibold small"><?= esc($user['name']) ?></div>
                <small class="text-muted"><?= esc($user['email'] ?? '') ?></small>
            </div>

            <nav class="flex-grow-1 overflow-auto">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <small class="px-3 py-1 text-uppercase text-muted fw-bold d-block">Akun Saya</small>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sidebar-link" href="<?= base_url('profile') ?>"><i class="bi bi-person me-2"></i>Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sidebar-link" href="<?= base_url('cart') ?>">
                            <i class="bi bi-cart3 me-2"></i>Keranjang
                            <span class="badge bg-danger rounded-pill float-end" id="cartBadgeSidebar" style="<?= get_cart_count() > 0 ? '' : 'display:none;' ?>"><?= get_cart_count() ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sidebar-link" href="<?= base_url('wishlist') ?>"><i class="bi bi-heart me-2"></i>Wishlist</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sidebar-link" href="<?= base_url('order') ?>"><i class="bi bi-bag me-2"></i>Pesanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sidebar-link" href="<?= base_url('address') ?>"><i class="bi bi-geo-alt me-2"></i>Alamat</a>
                    </li>

                    <?php if (is_seller()): ?>
                        <li class="nav-item mt-2">
                            <small class="px-3 py-1 text-uppercase text-muted fw-bold d-block">Seller</small>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-link" href="<?= base_url('seller/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                        </li>
                    <?php endif; ?>

                    <?php if (is_admin()): ?>
                        <li class="nav-item mt-2">
                            <small class="px-3 py-1 text-uppercase text-muted fw-bold d-block">Admin</small>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-link" href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-shield-lock me-2"></i>Panel Admin</a>
                        </li>
                    <?php endif; ?>

                    <?php if (!is_seller() && !is_admin()): ?>
                        <li class="nav-item">
                            <a class="nav-link sidebar-link" href="<?= base_url('seller/toko') ?>"><i class="bi bi-shop-window me-2"></i>Buka Toko</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="border-top px-3 py-2 sidebar-footer-links">
                <div class="d-flex flex-column gap-1">

                    <div class="small">
                        <a href="#" class="text-decoration-none text-muted"><i class="bi bi-question-circle me-1"></i>Cara Belanja</a>
                        <span class="text-muted mx-1">|</span>
                        <a href="#" class="text-decoration-none text-muted"><i class="bi bi-shop me-1"></i>Cara Jual</a>
                    </div>
                    <div class="small">
                        <a href="#" class="text-decoration-none text-muted"><i class="bi bi-shield-check me-1"></i>Privasi</a>
                        <span class="text-muted mx-1">|</span>
                        <a href="#" class="text-decoration-none text-muted"><i class="bi bi-file-text me-1"></i>Syarat</a>
                    </div>
                    <hr class="my-1">
                    <div class="small text-muted">
                        <i class="bi bi-envelope me-1"></i>support@morowalimart.com
                    </div>
                    <div class="small text-muted">
                        <i class="bi bi-telephone me-1"></i>0800-1234-5678
                    </div>
                </div>
            </div>
            <div class="border-top p-2 text-center small text-muted">
                &copy; <?= date('Y') ?> Morowalimart
            </div>
            <div class="border-top p-2">
                <a class="nav-link sidebar-link text-danger px-2 py-1" href="<?= base_url('logout') ?>">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </div>
        </aside>
    <?php endif; ?>

    <div id="page-content">
        <?= $this->renderSection('content') ?>
    </div><!-- /page-content -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const base_url = '<?= rtrim(base_url(), '/') ?>/';
        const csrfName = '<?= csrf_hash() ? csrf_token() : '' ?>';
        const csrfHash = '<?= csrf_hash() ?>';

        $.ajaxSetup({
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#loadingOverlay').addClass('show');
            },
            complete: function() {
                $('#loadingOverlay').removeClass('show');
            },
            error: function(xhr) {
                if (xhr.status === 401) window.location.href = '<?= base_url('login') ?>';
            }
        });

        function showToast(message, type = 'success') {
            const icons = {
                success: 'bi-check-circle-fill',
                error: 'bi-x-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info: 'bi-info-circle-fill'
            };
            const colors = {
                success: 'bg-success',
                error: 'bg-danger',
                warning: 'bg-warning',
                info: 'bg-primary'
            };
            const toast = $(`<div class="toast align-items-center text-white ${colors[type] || colors.info} border-0 show" role="alert">
        <div class="d-flex"><div class="toast-body"><i class="bi ${icons[type] || icons.info} me-2"></i>${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`);
            $('.toast-container').append(toast);
            setTimeout(() => toast.fadeOut(300, function() {
                $(this).remove();
            }), 3000);
        }

        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            const query = $('#searchInput').val().trim();
            if (query) window.location.href = '<?= base_url('search') ?>?q=' + encodeURIComponent(query);
        });

        document.addEventListener('DOMContentLoaded', function() {
            if ('IntersectionObserver' in window) {
                const imgObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                            }
                            imgObserver.unobserve(img);
                        }
                    });
                });
                document.querySelectorAll('img[data-src]').forEach(img => imgObserver.observe(img));
            }
        });

        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        function updateCartBadge(count) {
            if (count > 0) {
                $('#cartBadge').text(count).show();
                $('#cartBadgeMobile').text(count).show();
                $('#cartBadgeSidebar').text(count).show();
            } else {
                $('#cartBadge').hide();
                $('#cartBadgeMobile').hide();
                $('#cartBadgeSidebar').hide();
            }
        }

        function addToCart(productId, qty) {
            qty = qty || 1;
            $.post(
                base_url + 'cart/add', {
                    product_id: productId,
                    quantity: qty
                },
                function(res) {
                    if (!res.status && typeof showToast === 'function') {
                        showToast(res.message, 'error');
                    }
                    if (res.status && res.data && res.data.cart_count) {
                        updateCartBadge(res.data.cart_count);
                    }
                },
                'json'
            );
        }

        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        // Auto thousand-separator: tambahkan class "auto-separator" pada input
        $(document).on('input', '.auto-separator', function() {
            var raw = this.value.replace(/\D/g, '');
            this.value = raw ? Number(raw).toLocaleString('id-ID') : '';
        });
        $(document).on('submit', 'form', function() {
            $(this).find('.auto-separator').each(function() {
                this.value = this.value.replace(/\D/g, '');
            });
        });
    </script>

    <!-- View JS -->
    <?php if (isset($content)): ?>
        <?php if (isset($snapToken) && !empty($snapToken)): ?>
            <script src="<?= $snapUrl ?? '' ?>" data-client-key="<?= $clientKey ?? '' ?>"></script>
        <?php endif; ?>
        <script src="<?= asset_url('asset/js/view/' . ucfirst($content) . '.js') ?>"></script>
    <?php endif; ?>

    <?= $this->renderSection('scripts') ?>

</body>

</html>