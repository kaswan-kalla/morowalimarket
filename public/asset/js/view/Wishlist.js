function removeWishlist(id) {
  $.post(base_url + '/wishlist/toggle', { product_id: id }, function (res) {
    if (res.status) {
      $('#wishlist-' + id).fadeOut(300, function () {
        $(this).remove();
      });
      showToast('Dihapus dari wishlist', 'success');
    } else {
      showToast(res.message, 'danger');
    }
  });
}
