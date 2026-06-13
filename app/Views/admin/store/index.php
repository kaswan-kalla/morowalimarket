<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Kelola Toko<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <h4 class="mb-4">Kelola Toko</h4>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="searchStore" class="form-control" placeholder="Cari toko...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr><th>Toko</th><th>Pemilik</th><th>Kota</th><th>Status</th><th>Produk</th><th>Aksi</th></tr>
                        </thead>
                        <tbody id="storeBody"></tbody>
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
function loadStores() {
    $.get('<?= base_url('admin/stores/data') ?>', { search: $('#searchStore').val() }, function(res) {
        let html = '';
        res.data.forEach(function(s) {
            html += `<tr>
                <td><div class="d-flex align-items-center gap-2">
                    <img src="${base_url}uploads/stores/${s.logo || 'default.png'}" class="rounded" width="40" height="40" style="object-fit:cover">
                    <strong>${s.name}</strong></div></td>
                <td>${s.owner_name}</td><td>${s.city || '-'}</td>
                <td><span class="badge bg-${s.is_open ? 'success' : 'danger'}">${s.is_open ? 'Buka' : 'Tutup'}</span></td>
                <td>${s.product_count || 0}</td>
                <td><button class="btn btn-sm btn-outline-${s.is_open ? 'danger' : 'success'}" onclick="toggleStore(${s.id})">
                    ${s.is_open ? 'Tutup' : 'Buka'} Toko</button></td>
            </tr>`;
        });
        $('#storeBody').html(html || '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');
    });
}

function toggleStore(id) {
    $.post('<?= base_url('admin/stores/toggle') ?>', { id: id }, function(res) {
        if (res.status) { showToast('Status toko diperbarui', 'success'); loadStores(); }
        else showToast(res.message, 'danger');
    });
}

$('#searchStore').on('keyup', function() { clearTimeout(window.timer); window.timer = setTimeout(loadStores, 500); });
$(document).ready(loadStores);
</script>
<?= $this->endSection() ?>
