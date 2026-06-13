$('input[name="payment_method"]').on('change', function () {
  let method = $(this).val();
  $('#selectedMethod').val(method);
  $('#bankInfo').toggle(method === 'transfer');
  $('#qrisInfo').toggle(method === 'qris');
});

$('#paymentForm').on('submit', function (e) {
  e.preventDefault();
  $('#btnUploadProof')
    .prop('disabled', true)
    .html(
      '<span class="spinner-border spinner-border-sm"></span> Mengupload...',
    );
  $.ajax({
    url: base_url + '/payment/upload',
    method: 'POST',
    data: new FormData(this),
    processData: false,
    contentType: false,
    success: function (res) {
      if (res.status) {
        showToast('Bukti pembayaran diupload!', 'success');
        setTimeout(() => location.reload(), 1000);
      } else {
        showToast(res.message, 'danger');
        $('#btnUploadProof')
          .prop('disabled', false)
          .html('<i class="bi bi-upload"></i> Upload Bukti');
      }
    },
    error: function () {
      showToast('Gagal upload', 'danger');
      $('#btnUploadProof')
        .prop('disabled', false)
        .html('<i class="bi bi-upload"></i> Upload Bukti');
    },
  });
});
