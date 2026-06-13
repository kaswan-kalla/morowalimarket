<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Kelola Produk<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <h4 class="mb-4">Kelola Produk</h4>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3"><input type="text" id="searchProduct" class="form-control" placeholder="Cari produk..."></div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr><th>Gambar</th><th>Produk</th><th>Toko</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody id="productBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
function loadProducts() {
    $.get('<?= base_url('admin/products/data') ?>', { search: $('#searchProduct').val() }, function(res) {
        let html = '';
        res.data.forEach(function(p) {
            html += `<tr>
                <td><img src="${base_url}uploads/products/${p.main_image || 'default.png'}" class="rounded" width="50" height="50" style="object-fit:cover"></td>
                <td><strong>${p.name}</strong><br><small class="text-muted">${p.category_name || '-'}</small></td>
                <td>${p.store_name || '-'}</td>
                <td class="text-danger fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(p.discount_price > 0 ? p.discount_price : p.price)}</td>
                <td><span class="badge bg-${p.stock <= 5 ? 'danger' : 'success'}">${p.stock}</span></td>
                <td><span class="badge bg-${p.is_active ? 'success' : 'secondary'}">${p.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-${p.is_active ? 'warning' : 'success'}" onclick="toggleProduct(${p.id})">
                        ${p.is_active ? 'Nonaktifkan' : 'Aktifkan'}</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${p.id})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`;
        });
        $('#productBody').html(html || '<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>');
    });
}

function toggleProduct(id) {
    $.post('<?= base_url('admin/products/toggle') ?>', { id: id }, function(res) {
        if (res.status) { showToast('Status produk diperbarui', 'success'); loadProducts(); }
        else showToast(res.message, 'danger');
    });
}

function deleteProduct(id) {
    Swal.fire({ title: 'Hapus produk?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Batal'
    }).then(r => { if (r.isConfirmed) { $.post('<?= base_url('admin/products/delete') ?>', { id: id }, function(res) {
        if (res.status) { showToast('Produk dihapus', 'success'); loadProducts(); } else showToast(res.message, 'danger'); }); }});
}

$('#searchProduct').on('keyup', function() { clearTimeout(window.timer); window.timer = setTimeout(loadProducts, 500); });
$(document).ready(loadProducts);
</script>
<?= $this->endSection() ?>
