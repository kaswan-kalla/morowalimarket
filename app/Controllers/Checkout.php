<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\AddressModel;
use App\Models\ProductModel;
use App\Models\VoucherModel;
use App\Services\MidtransService;

/**
 * Controller Checkout - menggunakan database transaction
 */
class Checkout extends BaseController
{
    protected $cartModel, $cartItemModel, $orderModel, $orderItemModel;
    protected $addressModel, $productModel, $voucherModel;
    protected $midtransService;

    public function __construct()
    {
        $this->cartModel     = new CartModel();
        $this->cartItemModel = new CartItemModel();
        $this->orderModel    = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->addressModel  = new AddressModel();
        $this->productModel  = new ProductModel();
        $this->voucherModel  = new VoucherModel();
        $this->midtransService = new MidtransService();
    }

    /**
     * Halaman checkout
     */
    public function index()
    {
        $userId = $this->session->get('user_id');
        $cart   = $this->cartModel->getOrCreate($userId);
        $items  = $this->cartItemModel->getCartItems($cart['id']);

        if (empty($items)) {
            return redirect()->to('/cart')->with('error', 'Keranjang kosong');
        }

        $data = [
            'content'    => 'checkout',
            'meta_title' => 'Checkout',
            'cart_items' => $items,
            'addresses'  => $this->addressModel->getByUser($userId),
            'subtotal'   => $this->cartItemModel->getSubtotal($cart['id']),
            'snapUrl'    => $this->midtransService->getSnapUrl(),
            'clientKey'  => $this->midtransService->getClientKey(),
            'snapToken'  => 'preload', // trigger snap.js load di layout
        ];

        return view('layout/marketplace_content', $data);
    }

    /**
     * Proses checkout dengan database transaction
     */
    public function process()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $userId    = $this->session->get('user_id');
        $addressId = (int) $this->request->getPost('address_id');
        $courier   = $this->request->getPost('courier');
        $notes     = $this->request->getPost('notes');
        $voucher   = $this->request->getPost('voucher_code');

        // Validasi
        $address = $this->addressModel->find($addressId);
        if (!$address || $address['user_id'] != $userId) {
            return $this->response->setJSON(['status' => false, 'message' => 'Alamat tidak valid']);
        }

        $cart  = $this->cartModel->getOrCreate($userId);
        $items = $this->cartItemModel->getCartItems($cart['id']);

        if (empty($items)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Keranjang kosong']);
        }

        // Hitung total
        $subtotal      = $this->cartItemModel->getSubtotal($cart['id']);
        $shippingCost  = (float) $this->request->getPost('shipping_cost', 0);
        $discountAmount = 0;
        $voucherCode   = null;

        // Validasi voucher jika ada
        if ($voucher) {
            $voucherResult = $this->voucherModel->validateAndCalculate($voucher, $subtotal);
            if (!$voucherResult['valid']) {
                return $this->response->setJSON(['status' => false, 'message' => $voucherResult['message']]);
            }
            $discountAmount = $voucherResult['discount'];
            $voucherCode    = $voucher;
        }

        $total = $subtotal + $shippingCost - $discountAmount;

        // Mulai transaction
        $this->orderModel->db->transStart();

        // Buat order
        $orderId = $this->orderModel->insert([
            'order_number'    => $this->orderModel->generateOrderNumber(),
            'user_id'         => $userId,
            'address_id'      => $addressId,
            'subtotal'        => $subtotal,
            'shipping_cost'   => $shippingCost,
            'discount_amount' => $discountAmount,
            'voucher_code'    => $voucherCode,
            'total_amount'    => $total,
            'courier'         => $courier,
            'notes'           => $notes,
            'status'          => 'awaiting_payment',
        ]);

        // Simpan order items + kurangi stok
        $productIds = [];
        foreach ($items as $item) {
            $unitPrice = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
            $this->orderItemModel->insert([
                'order_id'      => $orderId,
                'product_id'    => $item['product_id'],
                'store_id'      => $item['store_id'],
                'product_name'  => $item['product_name'],
                'product_slug'  => $item['product_slug'],
                'product_image' => $item['main_image'],
                'price'         => $unitPrice,
                'qty'           => $item['qty'],
                'subtotal'      => $item['qty'] * $unitPrice,
            ]);

            // Kurangi stok
            $this->productModel->decreaseStock($item['product_id'], $item['qty']);
            $productIds[] = $item['product_id'];
        }

        // Increment voucher usage
        if ($voucherCode && isset($voucherResult['voucher'])) {
            $this->voucherModel->incrementUsage($voucherResult['voucher']['id']);
        }

        // Kosongkan cart
        $this->cartItemModel->clearCart($cart['id'], $productIds);

        $this->orderModel->db->transComplete();

        if ($this->orderModel->db->transStatus() === false) {
            return $this->response->setJSON(['status' => false, 'message' => 'Checkout gagal, silakan coba lagi']);
        }

        $order = $this->orderModel->find($orderId);

        // Generate Snap Token dari Midtrans
        $snapParams = [
            'transaction_details' => [
                'order_id'     => $order['order_number'],
                'gross_amount' => (int) $order['total_amount'],
            ],
            'customer_details' => [
                'first_name' => $this->session->get('user_name'),
                'email'      => $this->session->get('user_email'),
                'phone'      => $address['phone'] ?? '',
            ],
            'enabled_payments' => ['bri_va', 'gopay', 'shopeepay', 'qris'],
            'callbacks' => [
                'finish' => base_url('order/' . $orderId),
            ],
            'notification_url' => base_url('payment/notification'),
        ];

        $snapResult = $this->midtransService->createSnapToken($snapParams);
        $snapToken  = $snapResult['token'] ?? null;

        if ($snapToken) {
            $this->orderModel->update($orderId, ['snap_token' => $snapToken]);
        }

        return $this->response->setJSON([
            'status'   => true,
            'message'  => 'Checkout berhasil!',
            'redirect' => base_url('payment/' . $orderId),
            'data'     => [
                'order_id'     => $orderId,
                'order_number' => $order['order_number'],
                'snap_token'   => $snapToken,
            ]
        ]);
    }

    /**
     * Apply voucher (AJAX)
     */
    public function applyVoucher()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $code     = $this->request->getPost('code');
        $subtotal = (float) $this->request->getPost('subtotal');

        $result = $this->voucherModel->validateAndCalculate($code, $subtotal);

        if (!$result['valid']) {
            return $this->response->setJSON(['status' => false, 'message' => $result['message']]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Voucher berhasil diterapkan',
            'data'    => ['discount' => $result['discount']]
        ]);
    }
}
