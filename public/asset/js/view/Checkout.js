let subtotal = parseInt(
  $('#subtotal')
    .text()
    .replace(/[^0-9]/g, ''),
);
let discount = 0;
let shipping = 0;

$('#btnApplyVoucher').on('click', function () {
  let code = $('#voucherInput').val();
  if (!code) return;
  $.post(
    base_url + '/checkout/apply-voucher',
    { code: code, subtotal: subtotal },
    function (res) {
      if (res.status) {
        discount = res.data.discount;
        $('#voucherResult').html(
          '<small class="text-success"><i class="bi bi-check-circle"></i> ' +
            res.message +
            '</small>',
        );
        $('#discountRow').show();
        $('#discountAmount').text('- ' + formatRupiah(discount));
        calculateTotal();
      } else {
        discount = 0;
        $('#voucherResult').html(
          '<small class="text-danger"><i class="bi bi-x-circle"></i> ' +
            res.message +
            '</small>',
        );
        $('#discountRow').hide();
        calculateTotal();
      }
    },
  );
});

$('select[name="courier"]').on('change', function () {
  shipping = $(this).val() ? 15000 : 0;
  $('#shippingCost').text(formatRupiah(shipping));
  calculateTotal();
});

function calculateTotal() {
  let total = subtotal + shipping - discount;
  $('#grandTotal').text(formatRupiah(Math.max(total, 0)));
}

$('#checkoutForm').on('submit', function (e) {
  e.preventDefault();
  let addressId = $('input[name="address_id"]:checked').val();
  if (!addressId) {
    showToast('Pilih alamat pengiriman', 'warning');
    return;
  }

  $('#btnPlaceOrder')
    .prop('disabled', true)
    .html(
      '<span class="spinner-border spinner-border-sm"></span> Memproses...',
    );

  $.ajax({
    url: base_url + '/checkout/process',
    method: 'POST',
    data: $(this).serialize(),
    success: function (res) {
      if (res.status) {
        showToast('Pesanan berhasil dibuat!', 'success');
        window.location.href = res.redirect;
      } else {
        showToast(res.message, 'danger');
        $('#btnPlaceOrder')
          .prop('disabled', false)
          .html('<i class="bi bi-bag-check"></i> Buat Pesanan');
      }
    },
    error: function () {
      showToast('Terjadi kesalahan', 'danger');
      $('#btnPlaceOrder')
        .prop('disabled', false)
        .html('<i class="bi bi-bag-check"></i> Buat Pesanan');
    },
  });
});
