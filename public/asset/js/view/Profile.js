// Upload photo on file select
$('#photoInput').on('change', function () {
  let formData = new FormData($('#photoForm')[0]);
  formData.append('photo', this.files[0]);
  $.ajax({
    url: base_url + '/profile/upload-photo',
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      if (res.status) {
        showToast('Foto diperbarui', 'success');
        if (res.data.photo)
          $('#profilePreview').attr(
            'src',
            base_url + '/uploads/users/' + res.data.photo,
          );
      } else showToast(res.message, 'danger');
    },
  });
});

// Save profile
$('#profileForm').on('submit', function (e) {
  e.preventDefault();
  $.post(base_url + '/profile/update', $(this).serialize(), function (res) {
    if (res.status) showToast('Profil diperbarui', 'success');
    else showToast(res.message, 'danger');
  });
});

// Change password
$('#passwordForm').on('submit', function (e) {
  e.preventDefault();
  $.post(
    base_url + '/profile/change-password',
    $(this).serialize(),
    function (res) {
      if (res.status) {
        showToast('Password diubah', 'success');
        $('#passwordForm')[0].reset();
      } else showToast(res.message, 'danger');
    },
  );
});
