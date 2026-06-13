<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Kelola Toko<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Manajemen Toko</h4>
            <button class="btn btn-primary" onclick="showForm()"><i class="bi bi-plus-lg"></i> Tambah Toko</button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="searchStore" class="form-control" placeholder="Cari toko atau pemilik...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Toko</th>
                                <th>Pemilik</th>
                                <th>Kota</th>
                                <th>Status</th>
                                <th>Produk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="storeBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="storeModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="storeModalTitle">Tambah Toko</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="storeForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="storeId">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pemilik <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">-- Pilih Pemilik --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kota</label>
                            <input type="text" name="city" id="city" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Provinsi</label>
                            <input type="text" name="province" id="province" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="postal_code" id="postal_code" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" id="logo" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">Max 2MB</small>
                            <div id="previewLogo" class="mt-2"></div>
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_open" id="is_open" value="1" checked>
                                <label class="form-check-label" for="is_open">Buka</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSave"><i class="bi bi-floppy"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>

<?= $this->section('scripts') ?>
<script>
    function loadStores() {
        $.get('<?= base_url('admin/stores/data') ?>', {
            search: {
                value: $('#searchStore').val()
            }
        }, function(res) {
            let html = '';
            res.data.forEach(function(s) {
                let logo = s.logo ? base_url + s.logo : base_url + 'uploads/stores/default.png';
                html += `<tr>
                <td><div class="d-flex align-items-center gap-2">
                    <img src="${logo}" class="rounded" width="40" height="40" style="object-fit:cover">
                    <strong>${escHtml(s.name)}</strong></div></td>
                <td>${escHtml(s.owner_name)}</td>
                <td>${escHtml(s.city || '-')}</td>
                <td><span class="badge bg-${s.is_open ? 'success' : 'danger'}">${s.is_open ? 'Buka' : 'Tutup'}</span></td>
                <td>${s.product_count || 0}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editStore(${s.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-${s.is_open ? 'danger' : 'success'}" onclick="toggleStore(${s.id})" title="${s.is_open ? 'Tutup' : 'Buka'}">
                            <i class="bi bi-${s.is_open ? 'pause' : 'play'}"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteStore(${s.id})" title="Hapus"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`;
            });
            $('#storeBody').html(html || '<tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada data</td></tr>');
        });
    }

    function escHtml(str) {
        if (!str) return '';
        return $('<span>').text(str).html();
    }

    function loadUserSelect() {
        $.get('<?= base_url('admin/stores/get-users') ?>', function(res) {
            let opts = '<option value="">-- Pilih Pemilik --</option>';
            if (res.Options) {
                res.Options.forEach(function(o) {
                    opts += '<option value="' + o.Value + '">' + o.DisplayText + '</option>';
                });
            }
            $('#user_id').html(opts);
        });
    }

    function showForm(data) {
        $('#storeModalTitle').text('Tambah Toko');
        $('#storeForm')[0].reset();
        $('#storeId').val('');
        $('#previewLogo').html('');
        $('#btnSave').html('<i class="bi bi-floppy"></i> Simpan');
        $('#is_open').prop('checked', true);
        loadUserSelect();

        if (data) {
            $('#storeModalTitle').text('Edit Toko');
            $('#storeId').val(data.id);
            $('#name').val(data.name);
            $('#user_id').val(data.user_id);
            $('#city').val(data.city || '');
            $('#province').val(data.province || '');
            $('#postal_code').val(data.postal_code || '');
            $('#address').val(data.address || '');
            $('#description').val(data.description || '');
            $('#phone').val(data.phone || '');
            $('#is_open').prop('checked', data.is_open ? true : false);

            if (data.logo) {
                $('#previewLogo').html(`<img src="${base_url}${data.logo}" class="rounded" width="80" height="80" style="object-fit:cover">`);
            }
            $('#btnSave').html('<i class="bi bi-floppy"></i> Update');
        }

        new bootstrap.Modal($('#storeModal')).show();
    }

    function editStore(id) {
        $.get('<?= base_url('admin/stores/get') ?>/' + id, function(res) {
            if (res.status) {
                showForm(res.data);
            } else {
                showToast(res.message, 'error');
            }
        });
    }

    $('#storeForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#storeId').val();
        let url = id ? '<?= base_url('admin/stores/update') ?>/' + id : '<?= base_url('admin/stores/store') ?>';

        $.ajax({
            url: url,
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status) {
                    showToast(res.message, 'success');
                    bootstrap.Modal.getInstance($('#storeModal')[0]).hide();
                    loadStores();
                } else {
                    showToast(res.message, 'error');
                }
            },
            error: function() {
                showToast('Terjadi kesalahan server', 'error');
            }
        });
    });

    function toggleStore(id) {
        $.post('<?= base_url('admin/stores/toggle') ?>', {
            id: id
        }, function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                loadStores();
            } else {
                showToast(res.message, 'error');
            }
        });
    }

    function deleteStore(id) {
        Swal.fire({
            title: 'Hapus toko?',
            text: 'Data toko dan produk akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('<?= base_url('admin/stores/delete') ?>', {
                    id: id
                }, function(res) {
                    if (res.status) {
                        showToast(res.message, 'success');
                        loadStores();
                    } else {
                        showToast(res.message, 'error');
                    }
                });
            }
        });
    }

    $('#searchStore').on('keyup', function() {
        clearTimeout(window.searchTimer);
        window.searchTimer = setTimeout(loadStores, 500);
    });

    $(document).ready(loadStores);

    // Image preview
    $('#logo').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewLogo').html(`<img src="${e.target.result}" class="rounded" width="80" height="80" style="object-fit:cover">`);
            };
            reader.readAsDataURL(file);
        } else {
            $('#previewLogo').html('');
        }
    });
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>