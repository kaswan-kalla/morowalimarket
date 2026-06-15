<?= $this->extend('layouts/header') ?>
<?= $this->section('title') ?>Verifikasi Pembayaran<?= $this->endSection() ?>
<?= $this->include('layouts/navbar') ?>
<?= $this->include('layouts/sidebar') ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <h4 class="mb-4">Verifikasi Pembayaran</h4>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link active" data-status="pending" href="#">Pending</a></li>
            <li class="nav-item"><a class="nav-link" data-status="verified" href="#">Terverifikasi</a></li>
            <li class="nav-item"><a class="nav-link" data-status="rejected" href="#">Ditolak</a></li>
            <li class="nav-item"><a class="nav-link" data-status="all" href="#">Semua</a></li>
        </ul>

        <div id="paymentList"></div>
    </div>
</div>


<?= $this->include('layouts/scripts') ?>

<?= $this->section('scripts') ?>
<script>
    let currentStatus = 'pending';

    function loadPayments() {
        $.get('<?= base_url('admin/payments/data') ?>', {
            status: currentStatus
        }, function(res) {
            let html = '';
            res.data.forEach(function(p) {
                html += `<div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="${base_url}uploads/payments/${p.proof_image}" class="img-fluid rounded" style="max-height:120px;object-fit:cover" 
                                 onclick="window.open('${base_url}uploads/payments/${p.proof_image}','_blank')">
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>${p.order_number}</strong></p>
                            <p class="mb-1">Pembeli: ${p.buyer_name}</p>
                            <p class="mb-1">Metode: ${p.payment_method.toUpperCase()}</p>
                            <p class="mb-1 text-danger fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(p.amount)}</p>
                            <small class="text-muted">${new Date(p.created_at).toLocaleString('id-ID')}</small>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-${p.status === 'verified' ? 'success' : (p.status === 'rejected' ? 'danger' : 'warning')} px-3 py-2">
                                ${p.status.toUpperCase()}</span>
                        </div>
                        <div class="col-md-3">
                            ${p.status === 'pending' ? `
                                <button class="btn btn-success btn-sm w-100 mb-1" onclick="verifyPayment(${p.id}, 'verified')"><i class="bi bi-check"></i> Verifikasi</button>
                                <button class="btn btn-danger btn-sm w-100" onclick="verifyPayment(${p.id}, 'rejected')"><i class="bi bi-x"></i> Tolak</button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>`;
            });
            $('#paymentList').html(html || '<div class="text-center py-5 text-muted">Tidak ada data</div>');
        });
    }

    function verifyPayment(id, status) {
        let action = status === 'verified' ? 'memverifikasi' : 'menolak';
        Swal.fire({
            title: `${action.charAt(0).toUpperCase() + action.slice(1)} pembayaran?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then(r => {
            if (r.isConfirmed) {
                let notes = '';
                if (status === 'rejected') {
                    Swal.fire({
                            title: 'Alasan penolakan',
                            input: 'text',
                            showCancelButton: true,
                            confirmButtonText: 'Kirim'
                        })
                        .then(r2 => {
                            if (r2.isConfirmed) {
                                $.post('<?= base_url('admin/payments/verify') ?>', {
                                    id: id,
                                    status: status,
                                    notes: r2.value
                                }, function(res) {
                                    if (res.status) {
                                        showToast('Pembayaran ' + action, 'success');
                                        loadPayments();
                                    } else showToast(res.message, 'danger');
                                });
                            }
                        });
                    return;
                }
                $.post('<?= base_url('admin/payments/verify') ?>', {
                    id: id,
                    status: status,
                    notes: notes
                }, function(res) {
                    if (res.status) {
                        showToast('Pembayaran diverifikasi', 'success');
                        loadPayments();
                    } else showToast(res.message, 'danger');
                });
            }
        });
    }

    $('.nav-tabs .nav-link').on('click', function(e) {
        e.preventDefault();
        $('.nav-tabs .nav-link').removeClass('active');
        $(this).addClass('active');
        currentStatus = $(this).data('status');
        loadPayments();
    });

    $(document).ready(loadPayments);
</script>
<?= $this->endSection() ?>