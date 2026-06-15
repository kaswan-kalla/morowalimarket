<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Kelola Voucher<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Kelola Voucher</h4>
            <button class="btn btn-primary" onclick="showForm()"><i class="bi bi-plus"></i> Tambah Voucher</button>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Tipe</th>
                                <th>Nilai</th>
                                <th>Min. Belanja</th>
                                <th>Kuota</th>
                                <th>Expired</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="voucherBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="voucherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="voucherModalTitle">Tambah Voucher</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="voucherForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="voucherId">
                    <div class="mb-3"><label class="form-label">Kode Voucher</label><input type="text" name="code" class="form-control text-uppercase" required></div>
                    <div class="mb-3"><label class="form-label">Tipe Diskon</label>
                        <select name="discount_type" class="form-select" required>
                            <option value="percentage">Persentase (%)</option>
                            <option value="fixed">Nominal Tetap (Rp)</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Nilai</label><input type="number" name="discount_value" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Min. Belanja</label><input type="number" name="min_purchase" class="form-control" value="0"></div>
                    <div class="mb-3"><label class="form-label">Kuota Penggunaan</label><input type="number" name="max_usage" class="form-control" value="100"></div>
                    <div class="mb-3"><label class="form-label">Tanggal Kadaluarsa</label><input type="date" name="expired_at" class="form-control" required></div>
                    <div class="form-check"><input type="checkbox" name="is_active" class="form-check-input" id="voucherActive" value="1" checked><label class="form-check-label" for="voucherActive">Aktif</label></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>


<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
    function loadVouchers() {
        $.get('<?= base_url('admin/vouchers/data') ?>', function(res) {
            let html = '';
            res.data.forEach(function(v) {
                html += `<tr>
                <td><strong>${v.code}</strong></td>
                <td>${v.discount_type === 'percentage' ? 'Persentase' : 'Nominal'}</td>
                <td>${v.discount_type === 'percentage' ? v.discount_value + '%' : 'Rp ' + new Intl.NumberFormat('id-ID').format(v.discount_value)}</td>
                <td>Rp ${new Intl.NumberFormat('id-ID').format(v.min_purchase)}</td>
                <td>${v.used_count || 0} / ${v.max_usage}</td>
                <td>${new Date(v.expired_at).toLocaleDateString('id-ID')}</td>
                <td><span class="badge bg-${v.is_active ? 'success' : 'secondary'}">${v.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
                <td><div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="editVoucher(${v.id})"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger" onclick="deleteVoucher(${v.id})"><i class="bi bi-trash"></i></button>
                </div></td></tr>`;
            });
            $('#voucherBody').html(html || '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
        });
    }

    function showForm() {
        $('#voucherModalTitle').text('Tambah Voucher');
        $('#voucherForm')[0].reset();
        $('#voucherId').val('');
        new bootstrap.Modal($('#voucherModal')).show();
    }

    function editVoucher(id) {
        $.get('<?= base_url('admin/vouchers/get') ?>/' + id, function(res) {
            if (res.status) {
                let d = res.data;
                $('#voucherModalTitle').text('Edit Voucher');
                $('#voucherId').val(d.id);
                $('input[name="code"]').val(d.code);
                $('select[name="discount_type"]').val(d.discount_type);
                $('input[name="discount_value"]').val(d.discount_value);
                $('input[name="min_purchase"]').val(d.min_purchase);
                $('input[name="max_usage"]').val(d.max_usage);
                $('input[name="expired_at"]').val(d.expired_at);
                $('#voucherActive').prop('checked', d.is_active == 1);
                new bootstrap.Modal($('#voucherModal')).show();
            }
        });
    }

    $('#voucherForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#voucherId').val();
        let url = id ? '<?= base_url('admin/vouchers/update') ?>/' + id : '<?= base_url('admin/vouchers/store') ?>';
        $.post(url, $(this).serialize(), function(res) {
            if (res.status) {
                showToast(res.message, 'success');
                bootstrap.Modal.getInstance($('#voucherModal')[0]).hide();
                loadVouchers();
            } else showToast(res.message, 'danger');
        });
    });

    function deleteVoucher(id) {
        Swal.fire({
            title: 'Hapus voucher?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('<?= base_url('admin/vouchers/delete') ?>/' + id, function(res) {
                    if (res.status) {
                        showToast('Voucher dihapus', 'success');
                        loadVouchers();
                    } else showToast(res.message, 'danger');
                });
            }
        });
    }

    $(document).ready(loadVouchers);
</script>
<?= $this->endSection() ?>