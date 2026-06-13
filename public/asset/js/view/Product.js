// Product index - Filter
function applyFilter() {
  let params = new URLSearchParams(window.location.search);
  let sort = $('#sortSelect').val();
  let minPrice = $('#minPrice').val();
  let maxPrice = $('#maxPrice').val();
  if (sort) params.set('sort', sort);
  if (minPrice) params.set('min_price', minPrice);
  else params.delete('min_price');
  if (maxPrice) params.set('max_price', maxPrice);
  else params.delete('max_price');
  params.set('page', 1);
  window.location.href = window.location.pathname + '?' + params.toString();
}

$(document).on('change', '#sortSelect', applyFilter);

// Product detail - Qty, Cart, Buy
function changeQty(delta) {
  let input = $('#qty');
  let val = parseInt(input.val()) + delta;
  if (val < 1) val = 1;
  let maxStock = parseInt(input.attr('max'));
  if (val > maxStock) val = maxStock;
  input.val(val);
}

function addToCart(productId) {
  $.post(
    base_url + '/cart/add',
    { product_id: productId, quantity: $('#qty').val() },
    function (res) {
      if (res.status) {
        showToast(res.message, 'success');
        updateCartBadge(res.data.cart_count);
      } else {
        showToast(res.message, 'error');
      }
    },
  );
}

function buyNow(productId) {
  addToCart(productId);
  setTimeout(() => (window.location.href = base_url + '/checkout'), 500);
}

// Product search - Filter form submit
$(document).on('submit', '#filterForm', function (e) {
  e.preventDefault();
  let params = $(this).serialize();
  window.location.href = base_url + '/search?' + params;
});
