$('#orderTabs .nav-link').on('click', function (e) {
  e.preventDefault();
  $('#orderTabs .nav-link').removeClass('active');
  $(this).addClass('active');
  let status = $(this).data('status');
  if (status === 'all') {
    $('.order-item').show();
  } else {
    $('.order-item').hide();
    $('.order-item[data-status="' + status + '"]').show();
  }
});

function cancelOrder(id) {
  Swal.fire({
    title: 'Batalkan pesanan?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, batalkan',
    cancelButtonText: 'Tidak',
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(base_url + '/order/cancel', { id: id }, function (res) {
        if (res.status) {
          showToast('Pesanan dibatalkan', 'success');
          location.reload();
        } else showToast(res.message, 'danger');
      });
    }
  });
}

function showReviewForm(productId, orderId) {
  $('#reviewProductId').val(productId);
  $('#reviewOrderId').val(orderId);
  new bootstrap.Modal($('#reviewModal')).show();
}

$('#reviewForm').on('submit', function (e) {
  e.preventDefault();
  let formData = new FormData(this);
  $.ajax({
    url: base_url + '/review/submit',
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      if (res.status) {
        showToast('Ulasan dikirim!', 'success');
        bootstrap.Modal.getInstance($('#reviewModal')[0]).hide();
      } else showToast(res.message, 'danger');
    },
  });
});

function completeOrder(id) {
  Swal.fire({
    title: 'Konfirmasi pesanan selesai?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, selesai',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(base_url + '/order/complete', { id: id }, function (res) {
        if (res.status) {
          showToast('Pesanan selesai!', 'success');
          location.reload();
        } else showToast(res.message, 'danger');
      });
    }
  });
}
