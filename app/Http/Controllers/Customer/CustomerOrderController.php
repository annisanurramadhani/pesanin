<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Throwable;

class CustomerOrderController extends Controller
{
    /**
     * Menampilkan katalog menu berdasarkan QR Code.
     */
    public function menu(string $code)
    {
        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        $merchant = $qrCode->merchant;

        $categories = Category::where('merchant_id', $merchant->id)
            ->where('status', 'active')
            ->with([
                'menus' => function ($query) {
                    $query
                        ->where('status', 'available')
                        ->orderBy('name');
                }
            ])
            ->orderBy('name')
            ->get();

        $menus = Menu::where('merchant_id', $merchant->id)
            ->where('status', 'available')
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('customer.menu', compact(
            'qrCode',
            'merchant',
            'categories',
            'menus'
        ));
    }


    /**
     * Menambahkan menu ke keranjang.
     */
    public function addToCart(Request $request, string $code)
    {
        $request->validate([
            'menu_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        $menu = Menu::where('id', $request->menu_id)
            ->where('merchant_id', $qrCode->merchant_id)
            ->where('status', 'available')
            ->firstOrFail();

        if ($menu->stock <= 0) {
            return back()->with(
                'error',
                "Maaf, {$menu->name} sedang habis."
            );
        }

        if ($request->quantity > $menu->stock) {
            return back()->with(
                'error',
                "Maaf, stok {$menu->name} hanya tersisa {$menu->stock}."
            );
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$menu->id])) {

            $newQuantity =
                $cart[$menu->id] +
                $request->quantity;

            if ($newQuantity > $menu->stock) {
                return back()->with(
                    'error',
                    "Maaf, stok {$menu->name} hanya tersisa {$menu->stock}."
                );
            }

            $cart[$menu->id] = $newQuantity;

        } else {

            $cart[$menu->id] = $request->quantity;
        }

        session()->put('cart', $cart);

        return back()->with(
            'success',
            "{$menu->name} berhasil ditambahkan ke keranjang."
        );
    }


    /**
     * Menampilkan keranjang.
     */
    public function cart(string $code)
    {
        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        $merchant = $qrCode->merchant;

        $cart = session()->get('cart', []);

        $menus = Menu::whereIn('id', array_keys($cart))
            ->where('merchant_id', $merchant->id)
            ->where('status', 'available')
            ->get()
            ->keyBy('id');

        $cartItems = [];

        foreach ($cart as $menuId => $quantity) {

            if (!isset($menus[$menuId])) {
                continue;
            }

            $menu = $menus[$menuId];

            $cartItems[] = [
                'menu' => $menu,
                'quantity' => $quantity,
                'subtotal' => $menu->price * $quantity,
            ];
        }

        $total = collect($cartItems)->sum('subtotal');

        return view('customer.cart', compact(
            'qrCode',
            'merchant',
            'cartItems',
            'total'
        ));
    }


    /**
     * Mengubah jumlah menu di keranjang.
     */
    public function updateCart(Request $request, string $code)
    {
        $request->validate([
            'menu_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        $menu = Menu::where('id', $request->menu_id)
            ->where('merchant_id', $qrCode->merchant_id)
            ->where('status', 'available')
            ->firstOrFail();

        $cart = session()->get('cart', []);

        if ($request->quantity <= 0) {

            unset($cart[$menu->id]);

        } else {

            if ($request->quantity > $menu->stock) {
                return back()->with(
                    'error',
                    "Maaf, stok {$menu->name} hanya tersisa {$menu->stock}."
                );
            }

            $cart[$menu->id] = $request->quantity;
        }

        session()->put('cart', $cart);

        return back();
    }


    /**
     * Menghapus menu dari keranjang.
     */
    public function removeFromCart(string $code, int $menuId)
    {
        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        $menu = Menu::where('id', $menuId)
            ->where('merchant_id', $qrCode->merchant_id)
            ->firstOrFail();

        $cart = session()->get('cart', []);

        unset($cart[$menu->id]);

        session()->put('cart', $cart);

        return back()->with(
            'success',
            "{$menu->name} dihapus dari keranjang."
        );
    }


    /**
     * Menampilkan halaman checkout.
     */
    public function checkout(string $code)
    {
        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        $merchant = $qrCode->merchant;

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('customer.cart', $code)
                ->with(
                    'error',
                    'Keranjang masih kosong.'
                );
        }

        $menus = Menu::whereIn('id', array_keys($cart))
            ->where('merchant_id', $merchant->id)
            ->where('status', 'available')
            ->get()
            ->keyBy('id');

        $cartItems = [];

        foreach ($cart as $menuId => $quantity) {

            if (!isset($menus[$menuId])) {
                continue;
            }

            $menu = $menus[$menuId];

            $cartItems[] = [
                'menu' => $menu,
                'quantity' => $quantity,
                'subtotal' => $menu->price * $quantity,
            ];
        }

        $total = collect($cartItems)->sum('subtotal');

        return view('customer.checkout', compact(
            'qrCode',
            'merchant',
            'cartItems',
            'total'
        ));
    }


    /**
     * Membuat pesanan.
     */
    public function store(Request $request, string $code)
    {
        $request->validate([
            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'customer_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'customer_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'payment_method' => [
                'required',
                'in:qris,cash',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | QR CODE
        |--------------------------------------------------------------------------
        */

        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        $merchant = $qrCode->merchant;


        /*
        |--------------------------------------------------------------------------
        | CART
        |--------------------------------------------------------------------------
        */

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('customer.cart', $code)
                ->with(
                    'error',
                    'Keranjang masih kosong.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL MENU
        |--------------------------------------------------------------------------
        */

        $menus = Menu::whereIn('id', array_keys($cart))
            ->where('merchant_id', $merchant->id)
            ->where('status', 'available')
            ->get()
            ->keyBy('id');

        if ($menus->isEmpty()) {
            return redirect()
                ->route('customer.cart', $code)
                ->with(
                    'error',
                    'Menu dalam keranjang sudah tidak tersedia.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI STOCK & HITUNG SUBTOTAL
        |--------------------------------------------------------------------------
        */

        $subtotal = 0;

        foreach ($cart as $menuId => $quantity) {

            if (!isset($menus[$menuId])) {
                continue;
            }

            $menu = $menus[$menuId];

            if ($quantity > $menu->stock) {

                return redirect()
                    ->route('customer.cart', $code)
                    ->with(
                        'error',
                        "Stok {$menu->name} tidak mencukupi."
                    );
            }

            $subtotal +=
                $menu->price * $quantity;
        }


        /*
        |--------------------------------------------------------------------------
        | GENERATE ORDER NUMBER
        |--------------------------------------------------------------------------
        */

        $orderNumber =
            'ORD-' .
            now()->format('YmdHis') .
            '-' .
            strtoupper(str()->random(6));


        /*
        |--------------------------------------------------------------------------
        | CREATE ORDER
        |--------------------------------------------------------------------------
        */

        $order = DB::transaction(function () use (
            $request,
            $qrCode,
            $merchant,
            $cart,
            $menus,
            $subtotal,
            $orderNumber
        ) {

            $order = Order::create([

                'merchant_id' =>
                    $merchant->id,

                'qr_code_id' =>
                    $qrCode->id,

                'order_number' =>
                    $orderNumber,

                'customer_name' =>
                    $request->customer_name,

                'customer_phone' =>
                    $request->customer_phone,

                'customer_email' =>
                    $request->customer_email,

                'subtotal' =>
                    $subtotal,

                'total' =>
                    $subtotal,

                'payment_method' =>
                    $request->payment_method,

                'payment_provider' => null,

                'status' =>
                    'pending',
            ]);


            /*
            |--------------------------------------------------------------------------
            | CREATE ORDER ITEMS
            |--------------------------------------------------------------------------
            */

            foreach ($cart as $menuId => $quantity) {

                if (!isset($menus[$menuId])) {
                    continue;
                }

                $menu = $menus[$menuId];

                OrderItem::create([

                    'order_id' =>
                        $order->id,

                    'menu_id' =>
                        $menu->id,

                    'menu_name' =>
                        $menu->name,

                    'quantity' =>
                        $quantity,

                    'price' =>
                        $menu->price,

                    'subtotal' =>
                        $menu->price * $quantity,
                ]);
            }

            return $order;
        });


        /*
        |--------------------------------------------------------------------------
        | CASH
        |--------------------------------------------------------------------------
        */

        if ($request->payment_method === 'cash') {

            session()->forget('cart');

            return redirect()
                ->route('customer.order.success', [
                    'code' =>
                        $code,

                    'orderNumber' =>
                        $order->order_number,
                ])
                ->with(
                    'success',
                    'Pesanan berhasil dibuat.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | MIDTRANS CONFIG
        |--------------------------------------------------------------------------
        */

        Config::$serverKey = config(
            'services.midtrans.server_key'
        );

        Config::$clientKey = config(
            'services.midtrans.client_key'
        );

        Config::$isProduction = config(
            'services.midtrans.is_production',
            false
        );

        Config::$isSanitized = true;
        Config::$is3ds = true;


        /*
        |--------------------------------------------------------------------------
        | VALIDASI SERVER KEY
        |--------------------------------------------------------------------------
        */

        if (empty(Config::$serverKey)) {

            Log::error(
                'Midtrans Server Key belum dikonfigurasi.'
            );

            return back()
                ->with(
                    'error',
                    'Konfigurasi pembayaran belum tersedia.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | MIDTRANS ORDER ID
        |--------------------------------------------------------------------------
        */

        $midtransOrderId =
            'ORD-' .
            $order->id .
            '-' .
            now()->format('YmdHis') .
            '-' .
            strtoupper(str()->random(6));


        /*
        |--------------------------------------------------------------------------
        | ITEM DETAILS
        |--------------------------------------------------------------------------
        */

        $itemDetails = [];

        foreach ($cart as $menuId => $quantity) {

            if (!isset($menus[$menuId])) {
                continue;
            }

            $menu = $menus[$menuId];

            $itemDetails[] = [

                'id' =>
                    'MENU-' . $menu->id,

                'price' =>
                    (int) $menu->price,

                'quantity' =>
                    (int) $quantity,

                'name' =>
                    $menu->name,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | QRIS PARAMETER
        |--------------------------------------------------------------------------
        */

        $params = [

            'payment_type' =>
                'qris',

            'transaction_details' => [

                'order_id' =>
                    $midtransOrderId,

                'gross_amount' =>
                    (int) $order->total,
            ],

            'item_details' =>
                $itemDetails,

            'customer_details' => [

                'first_name' =>
                    $order->customer_name,

                'email' =>
                    $order->customer_email,

                'phone' =>
                    $order->customer_phone,
            ],

        ];


        /*
        |--------------------------------------------------------------------------
        | CHARGE QRIS
        |--------------------------------------------------------------------------
        */

        try {

            $response = CoreApi::charge($params);

            Log::info('MIDTRANS QRIS DATA', [
    'qr_string' => $response->qr_string ?? null,
    'actions' => $response->actions ?? null,
]);


        } catch (Throwable $e) {

            Log::error(
                'Midtrans QRIS Charge Error',
                [

                    'order_id' =>
                        $order->id,

                    'midtrans_order_id' =>
                        $midtransOrderId,

                    'message' =>
                        $e->getMessage(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );

            return back()->with(
                'error',
                'Gagal membuat pembayaran QRIS.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LOG RESPONSE MIDTRANS
        |--------------------------------------------------------------------------
        */

        Log::info(
            'MIDTRANS QRIS RESPONSE',
            [
                'order_id' =>
                    $order->id,

                'midtrans_order_id' =>
                    $midtransOrderId,

                'response' =>
                    json_encode($response),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDASI RESPONSE
        |--------------------------------------------------------------------------
        */

        if (empty($response->transaction_id)) {

            Log::error(
                'Midtrans transaction_id tidak ditemukan.',
                [
                    'order_id' =>
                        $order->id,

                    'response' =>
                        json_encode($response),
                ]
            );

            return back()->with(
                'error',
                'Transaksi QRIS gagal dibuat.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL QR CODE URL
        |--------------------------------------------------------------------------
        |
        | Midtrans mengembalikan actions:
        |
        | generate-qr-code
        | generate-qr-code-v2
        |
        */

        $actions = collect(
            $response->actions ?? []
        );

        $qrCodeAction =
            $actions->firstWhere(
                'name',
                'generate-qr-code-v2'
            );

        if (!$qrCodeAction) {

            $qrCodeAction =
                $actions->firstWhere(
                    'name',
                    'generate-qr-code'
                );
        }

        $qrCodeUrl =
            $qrCodeAction->url ?? null;


        /*
        |--------------------------------------------------------------------------
        | VALIDASI QR CODE URL
        |--------------------------------------------------------------------------
        */

        if (empty($qrCodeUrl)) {

            Log::error(
                'Midtrans QR Code URL tidak ditemukan.',
                [
                    'order_id' =>
                        $order->id,

                    'transaction_id' =>
                        $response->transaction_id,

                    'actions' =>
                        json_encode(
                            $response->actions ?? []
                        ),
                ]
            );

            return back()->with(
                'error',
                'QR Code gagal dibuat oleh Midtrans.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE PAYMENT PROVIDER
        |--------------------------------------------------------------------------
        */

        $order->update([

    'payment_provider' =>
        'midtrans:' . $midtransOrderId,
]);

        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA MIDTRANS KE SESSION
        |--------------------------------------------------------------------------
        */

        session()->put(
            'midtrans_order_' . $order->id,
            [

                'midtrans_order_id' =>
                    $midtransOrderId,

                'transaction_id' =>
                    $response->transaction_id,

                'qr_string' =>
                    $response->qr_string ?? null,

                'qr_code_url' =>
                    $qrCodeUrl,

                'transaction_status' =>
                    $response->transaction_status
                    ?? 'pending',

                'expiry_time' =>
                    $response->expiry_time
                    ?? null,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | HAPUS CART
        |--------------------------------------------------------------------------
        */

        session()->forget('cart');


        /*
        |--------------------------------------------------------------------------
        | LANGSUNG KE HALAMAN SUCCESS
        |--------------------------------------------------------------------------
        |
        | QRIS akan langsung tampil di order-success.blade.php
        |
        */

        return redirect()
            ->route('customer.order.success', [

                'code' =>
                    $code,

                'orderNumber' =>
                    $order->order_number,

            ])
            ->with(
                'success',
                'Pesanan berhasil dibuat. Silakan scan QRIS untuk membayar.'
            );
    }


    /**
     * Detail / Status Pesanan.
     */
    public function success(
        string $code,
        string $orderNumber
    ) {

        /*
        |--------------------------------------------------------------------------
        | QR CODE
        |--------------------------------------------------------------------------
        */

        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | MERCHANT
        |--------------------------------------------------------------------------
        */

        $merchant = $qrCode->merchant;


        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $order = Order::with('items')
            ->where('order_number', $orderNumber)
            ->where('merchant_id', $merchant->id)
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | PAYMENT DATA
        |--------------------------------------------------------------------------
        */

        $payment = session()->get(
            'midtrans_order_' . $order->id
        );


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'customer.order-success',
            compact(
                'qrCode',
                'merchant',
                'order',
                'payment'
            )
        );
    }
}
