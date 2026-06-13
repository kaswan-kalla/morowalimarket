function toggleWishlist(productId, btn) {
  $.post(
    base_url + '/wishlist/toggle',
    { product_id: productId },
    function (res) {
      showToast(res.message, 'success');
      $(btn).find('i').toggleClass('bi-heart bi-heart-fill');
      $(btn).toggleClass('active');
    },
  );
}
