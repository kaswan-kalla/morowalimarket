<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Alamat Saya<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-geo-alt"></i> Alamat Saya</h4>
        <button class="btn btn-primary btn-sm" onclick="showForm()">
            <i class="bi bi-plus"></i> Tambah Alamat
        </button>
    </div>

    <div id="addressList">
        <?php if (!empty($addresses)): ?>
            <?php foreach ($addresses as $a): ?>
                <div class="card shadow-sm mb-3" id="addr-<?= $a['id'] ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong><?= esc($a['label']) ?></strong>
                                <?php if ($a['is_default']): ?>
                                    <span class="badge bg-success ms-1">Utama</span>
                                <?php endif ?>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="editAddress(<?= $a['id'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteAddress(<?= $a['id'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <p class="mb-1 mt-2"><?= esc($a['recipient_name']) ?> - <?= esc($a['phone']) ?></p>
                        <p class="text-muted mb-0 small"><?= esc($a['address']) ?>, <?= esc($a['city']) ?>, <?= esc($a['province']) ?> <?= esc($a['postal_code']) ?></p>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-geo-alt fs-1 text-muted"></i>
                <p class="text-muted mt-2">Belum ada alamat tersimpan</p>
            </div>
        <?php endif ?>
    </div>
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Alamat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addressForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="addressId">
                    <div class="mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" name="label" class="form-control" placeholder="Rumah, Kantor, dll" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Penerima</label>
                        <input type="text" name="recipient_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="address" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kota</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Provinsi</label>
                            <input type="text" name="province" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" name="postal_code" class="form-control" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_default" class="form-check-input" id="isDefault" value="1">
                        <label class="form-check-label" for="isDefault">Jadikan alamat utama</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>
<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
function showForm() {
    $('#modalTitle').text('Tambah Alamat');
    $('#addressForm')[0].reset();
    $('#addressId').val('');
    new bootstrap.Modal($('#addressModal')).show();
}

function editAddress(id) {
    $.get('<?= base_url('address/get') ?>/' + id, function(res) {
        if (res.status) {
            let d = res.data;
            $('#modalTitle').text('Edit Alamat');
            $('#addressId').val(d.id);
            $('input[name="label"]').val(d.label);
            $('input[name="recipient_name"]').val(d.recipient_name);
            $('input[name="phone"]').val(d.phone);
            $('textarea[name="address"]').val(d.address);
            $('input[name="city"]').val(d.city);
            $('input[name="province"]').val(d.province);
            $('input[name="postal_code"]').val(d.postal_code);
            $('#isDefault').prop('checked', d.is_default == 1);
            new bootstrap.Modal($('#addressModal')).show();
        }
    });
}

$('#addressForm').on('submit', function(e) {
    e.preventDefault();
    let id = $('#addressId').val();
    let url = id ? '<?= base_url('address/update') ?>/' + id : '<?= base_url('address/save') ?>';
    $.post(url, $(this).serialize(), function(res) {
        if (res.status) { showToast('Alamat disimpan', 'success'); location.reload(); }
        else showToast(res.message, 'danger');
    });
});

function deleteAddress(id) {
    Swal.fire({
        title: 'Hapus alamat?', icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            $.post('<?= base_url('address/delete') ?>/' + id, {}, function(res) {
                if (res.status) { showToast('Alamat dihapus', 'success'); $('#addr-' + id).fadeOut(); }
                else showToast(res.message, 'danger');
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
