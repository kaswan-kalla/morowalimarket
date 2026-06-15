<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Kelola Kategori<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Kelola Kategori</h4>
            <button class="btn btn-primary" onclick="showForm()"><i class="bi bi-plus"></i> Tambah Kategori</button>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Icon</th>
                                <th>Nama</th>
                                <th>Slug</th>
                                <th>Produk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="catBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="catModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="catModalTitle">Tambah Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="catForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="catId">
                    <div class="mb-3"><label class="form-label">Nama Kategori</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Icon</label><input type="file" name="icon" class="form-control" accept="image/*"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>



<?= $this->section('scripts') ?>
<script>
    function loadCategories() {
        $.get('<?= base_url('admin/categories/data') ?>', function(res) {
            let html = '';
            res.data.forEach(function(c) {
                html += `<tr>
                <td>${c.icon ? '<img src="'+base_url+'uploads/categories/'+c.icon+'" width="40" height="40" class="rounded">' : '<i class="bi bi-folder fs-3 text-muted"></i>'}</td>
                <td><strong>${c.name}</strong></td><td>${c.slug}</td><td>${c.product_count || 0}</td>
                <td><div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="editCat(${c.id})"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger" onclick="deleteCat(${c.id})"><i class="bi bi-trash"></i></button>
                </div></td></tr>`;
            });
            $('#catBody').html(html || '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>');
        });
    }

    function showForm() {
        $('#catModalTitle').text('Tambah Kategori');
        $('#catForm')[0].reset();
        $('#catId').val('');
        new bootstrap.Modal($('#catModal')).show();
    }

    function editCat(id) {
        $.get('<?= base_url('admin/categories/get') ?>/' + id, function(res) {
            if (res.status) {
                $('#catModalTitle').text('Edit Kategori');
                $('#catId').val(res.data.id);
                $('input[name="name"]').val(res.data.name);
                new bootstrap.Modal($('#catModal')).show();
            }
        });
    }

    $('#catForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#catId').val();
        let url = id ? '<?= base_url('admin/categories/update') ?>/' + id : '<?= base_url('admin/categories/store') ?>';
        $.ajax({
            url: url,
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status) {
                    showToast(res.message, 'success');
                    bootstrap.Modal.getInstance($('#catModal')[0]).hide();
                    loadCategories();
                } else showToast(res.message, 'danger');
            }
        });
    });

    function deleteCat(id) {
        Swal.fire({
            title: 'Hapus kategori?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('<?= base_url('admin/categories/delete') ?>/' + id, function(res) {
                    if (res.status) {
                        showToast('Kategori dihapus', 'success');
                        loadCategories();
                    } else showToast(res.message, 'danger');
                });
            }
        });
    }

    $(document).ready(loadCategories);
</script>
<?= $this->endSection() ?>
<?= $this->include('layouts/scripts') ?>