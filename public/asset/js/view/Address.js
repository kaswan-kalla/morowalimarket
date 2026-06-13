function showForm() {
  $('#modalTitle').text('Tambah Alamat');
  $('#addressForm')[0].reset();
  $('#addressId').val('');
  new bootstrap.Modal($('#addressModal')).show();
}

function editAddress(id) {
  $.get(base_url + '/address/get/' + id, function (res) {
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

$('#addressForm').on('submit', function (e) {
  e.preventDefault();
  let id = $('#addressId').val();
  let url = id
    ? base_url + '/address/update/' + id
    : base_url + '/address/save';
  $.post(url, $(this).serialize(), function (res) {
    if (res.status) {
      showToast('Alamat disimpan', 'success');
      location.reload();
    } else showToast(res.message, 'danger');
  });
});

function deleteAddress(id) {
  Swal.fire({
    title: 'Hapus alamat?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(base_url + '/address/delete/' + id, {}, function (res) {
        if (res.status) {
          showToast('Alamat dihapus', 'success');
          $('#addr-' + id).fadeOut();
        } else showToast(res.message, 'danger');
      });
    }
  });
}
