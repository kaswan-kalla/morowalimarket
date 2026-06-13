function loadCart() {
  $.get(base_url + '/cart/summary', function (res) {
    let html = '';
    let totalItems = 0,
      totalPrice = 0;

    if (res.data.items && res.data.items.length > 0) {
      res.data.items.forEach(function (item) {
        let price = item.discount_price > 0 ? item.discount_price : item.price;
        let subtotal = price * item.qty;
        totalItems += parseInt(item.qty);
        totalPrice += subtotal;

        html += `
                <div class="border-bottom p-3 d-flex align-items-center gap-3">
                    <img src="${base_url}uploads/products/${item.image || 'default.png'}" 
                         class="rounded" width="80" height="80" style="object-fit:cover">
                    <div class="flex-grow-1">
                        <a href="${base_url}produk/${item.product_slug}" class="text-dark text-decoration-none fw-semibold">
                            ${item.product_name}
                        </a>
                        <br><small class="text-muted">${item.store_name}</small>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="updateQty(${item.id}, ${parseInt(item.qty) - 1})">-</button>
                            <input type="number" class="form-control form-control-sm text-center" style="width:60px" 
                                   value="${item.qty}" onchange="updateQty(${item.id}, this.value)" min="1">
                            <button class="btn btn-outline-secondary btn-sm" onclick="updateQty(${item.id}, ${parseInt(item.qty) + 1})">+</button>
                        </div>
                    </div>
                    <div class="text-end">
                        <p class="text-danger fw-bold mb-1">${formatRupiah(subtotal)}</p>
                        <button class="btn btn-sm text-danger" onclick="removeItem(${item.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>`;
      });
    } else {
      html =
        '<div class="text-center py-5"><i class="bi bi-cart-x fs-1 text-muted"></i><p class="text-muted mt-2">Keranjang kosong</p></div>';
      $('#btnCheckout').addClass('disabled');
    }

    $('#cartItems').html(html);
    $('#totalItems').text(totalItems + ' item');
    $('#totalPrice').text(formatRupiah(totalPrice));
  });
}

function updateQty(id, qty) {
  if (qty < 1) return;
  $.ajax({
    url: base_url + '/cart/update',
    method: 'POST',
    data: { id: id, qty: qty },
    success: function (res) {
      if (res.status) {
        showToast('Jumlah diperbarui', 'success');
        loadCart();
        updateCartBadge();
      } else showToast(res.message, 'danger');
    },
  });
}

function removeItem(id) {
  Swal.fire({
    title: 'Hapus item?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: base_url + '/cart/remove',
        method: 'POST',
        data: { id: id },
        success: function (res) {
          if (res.status) {
            showToast('Item dihapus', 'success');
            loadCart();
            updateCartBadge();
          } else showToast(res.message, 'danger');
        },
      });
    }
  });
}

$(document).ready(function () {
  loadCart();
});
