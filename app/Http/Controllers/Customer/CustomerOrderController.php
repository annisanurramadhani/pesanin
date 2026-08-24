<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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
            'menu_id'  => ['required', 'integer', 'exists:menus,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ], [
            'menu_id.required'  => 'Menu tidak valid.',
            'quantity.required' => 'Jumlah pesanan wajib diisi.',
            'quantity.min'      => 'Jumlah minimal pesanan adalah 1.',
            'quantity.max'      => 'Jumlah pesanan melebihi batas per item.',
        ]);

        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        $menu = Menu::where('id', $request->menu_id)
            ->where('merchant_id', $qrCode->merchant_id)
            ->where('status', 'available')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | CEK STOCK
        |--------------------------------------------------------------------------
        */

        if ($menu->stock <= 0) {

            return response()->json([
                'success' => false,
                'message' => "Maaf, {$menu->name} sedang habis."
            ], 422);
        }

        if ($request->quantity > $menu->stock) {

            return response()->json([
                'success' => false,
                'message' => "Maaf, stok {$menu->name} hanya tersisa {$menu->stock}."
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | CART
        |--------------------------------------------------------------------------
        */

        $cart = session()->get('cart', []);


        /*
        |--------------------------------------------------------------------------
        | MENU SUDAH ADA DI CART
        |--------------------------------------------------------------------------
        */

        if (isset($cart[$menu->id])) {
            $newQuantity = $cart[$menu->id] + $request->quantity;

            if ($newQuantity > $menu->stock) {

                return response()->json([
                    'success' => false,
                    'message' => "Maaf, stok {$menu->name} hanya tersisa {$menu->stock}."
                ], 422);
            }

            $cart[$menu->id] = $newQuantity;
        } else {
            $cart[$menu->id] = $request->quantity;
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN CART
        |--------------------------------------------------------------------------
        */

        session()->put('cart', $cart);


        /*
        |--------------------------------------------------------------------------
        | RESPONSE AJAX
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' => "{$menu->name} berhasil ditambahkan ke keranjang.",
            'cart_count' => array_sum($cart),
        ]);
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
                'menu'     => $menu,
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
            'quantity' => ['required', 'integer', 'min:0', 'max:50'],
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
                return response()->json([
                    'success' => false,
                    'message' => "Maaf, stok {$menu->name} hanya tersisa {$menu->stock}.",
                ], 422);
            }

            $cart[$menu->id] = $request->quantity;
        }

        session()->put('cart', $cart);

        $items = [];

        foreach ($cart as $menuId => $quantity) {
            $cartMenu = Menu::where('id', $menuId)
                ->where('merchant_id', $qrCode->merchant_id)
                ->where('status', 'available')
                ->first();

            if (!$cartMenu) {
                continue;
            }

            $items[] = [
                'menu_id' => $cartMenu->id,
                'quantity' => $quantity,
                'subtotal' => $cartMenu->price * $quantity,
            ];
        }

        $total = collect($items)->sum('subtotal');

        $currentItem = collect($items)->firstWhere('menu_id', $menu->id);

        return response()->json([
            'success' => true,
            'quantity' => $currentItem['quantity'] ?? 0,
            'subtotal' => $currentItem['subtotal'] ?? 0,
            'total' => $total,
            'removed' => !isset($cart[$menu->id]),
        ]);
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
                'menu'     => $menu,
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
        // Validasi Input Keamanan Server-Side
        $validated = $request->validate([
            'customer_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z\s]+$/', // Hanya diperbolehkan huruf dan spasi
            ],
            'customer_phone' => [
                'nullable',
                'string',
                'digits_between:10,14', // Hanya format angka telepon yang sah
            ],
            'customer_email' => [
                'nullable',
                'email:dns',
                'max:100',
            ],
            'payment_method' => [
                'required',
                'in:qris,cash',
            ],
        ], [
            'customer_name.required' => 'Nama pemesan wajib diisi.',
            'customer_name.regex'    => 'Nama pemesan hanya boleh berisi huruf dan spasi.',
            'customer_name.max'      => 'Nama pemesan maksimal 50 karakter.',
            'customer_phone.digits_between' => 'Nomor telepon harus berupa angka antara 10-14 digit.',
            'customer_email.email'   => 'Format email tidak valid.',
            'payment_method.in'      => 'Metode pembayaran tidak valid.',
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
        | VALIDASI STOCK & HITUNG SUBTOTAL REAL
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

            $subtotal += $menu->price * $quantity;
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE ORDER NUMBER
        |--------------------------------------------------------------------------
        */
        $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(str()->random(6));

        /*
        |--------------------------------------------------------------------------
        | CREATE ORDER (TRANSACTION)
        |--------------------------------------------------------------------------
        */
        $order = DB::transaction(function () use (
            $validated,
            $qrCode,
            $merchant,
            $cart,
            $menus,
            $subtotal,
            $orderNumber
        ) {
            $order = Order::create([
                'merchant_id'    => $merchant->id,
                'qr_code_id'     => $qrCode->id,
                'order_number'   => $orderNumber,
                'customer_name'  => strip_tags($validated['customer_name']), // Sanitasi Anti-XSS
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'subtotal'       => $subtotal,
                'total'          => $subtotal,
                'payment_method' => $validated['payment_method'],
                'payment_provider' => null,
                'status'         => 'pending',
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
                    'order_id'  => $order->id,
                    'menu_id'   => $menu->id,
                    'menu_name' => $menu->name,
                    'quantity'  => $quantity,
                    'price'     => $menu->price,
                    'subtotal'  => $menu->price * $quantity,
                ]);
            }

            return $order;
        });

        // Enkripsi Order Number untuk Keamanan URL Redirect
        $encryptedOrderNumber = Crypt::encryptString($order->order_number);

        /*
        |--------------------------------------------------------------------------
        | CASH
        |--------------------------------------------------------------------------
        */
        if ($validated['payment_method'] === 'cash') {
            session()->forget('cart');

            return redirect()
                ->route('customer.order.success', [
                    'code'        => $code,
                    'orderNumber' => $encryptedOrderNumber,
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
        Config::$serverKey     = config('services.midtrans.server_key');
        Config::$clientKey     = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        /*
        |--------------------------------------------------------------------------
        | VALIDASI SERVER KEY
        |--------------------------------------------------------------------------
        */
        if (empty(Config::$serverKey)) {
            Log::error('Midtrans Server Key belum dikonfigurasi.');

            return back()->with(
                'error',
                'Konfigurasi pembayaran belum tersedia.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MIDTRANS ORDER ID
        |--------------------------------------------------------------------------
        */
        $midtransOrderId = 'ORD-' . $order->id . '-' . now()->format('YmdHis') . '-' . strtoupper(str()->random(6));

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
                'id'       => 'MENU-' . $menu->id,
                'price'    => (int) $menu->price,
                'quantity' => (int) $quantity,
                'name'     => $menu->name,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | QRIS PARAMETER
        |--------------------------------------------------------------------------
        */
        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id'     => $midtransOrderId,
                'gross_amount' => (int) $order->total,
            ],
            'item_details'     => $itemDetails,
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email'      => $order->customer_email,
                'phone'      => $order->customer_phone,
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
                'actions'   => $response->actions ?? null,
            ]);
        } catch (Throwable $e) {
            Log::error('Midtrans QRIS Charge Error', [
                'order_id'          => $order->id,
                'midtrans_order_id' => $midtransOrderId,
                'message'           => $e->getMessage(),
                'trace'             => $e->getTraceAsString(),
            ]);

            return back()->with(
                'error',
                'Gagal membuat pembayaran QRIS.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI RESPONSE
        |--------------------------------------------------------------------------
        */
        if (empty($response->transaction_id)) {
            Log::error('Midtrans transaction_id tidak ditemukan.', [
                'order_id' => $order->id,
                'response' => json_encode($response),
            ]);

            return back()->with(
                'error',
                'Transaksi QRIS gagal dibuat.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL QR CODE URL
        |--------------------------------------------------------------------------
        */
        $actions = collect($response->actions ?? []);

        $qrCodeAction = $actions->firstWhere('name', 'generate-qr-code-v2');

        if (!$qrCodeAction) {
            $qrCodeAction = $actions->firstWhere('name', 'generate-qr-code');
        }

        $qrCodeUrl = $qrCodeAction->url ?? null;

        if (empty($qrCodeUrl)) {
            Log::error('Midtrans QR Code URL tidak ditemukan.', [
                'order_id'       => $order->id,
                'transaction_id' => $response->transaction_id,
                'actions'        => json_encode($response->actions ?? []),
            ]);

            return back()->with(
                'error',
                'QR Code gagal dibuat oleh Midtrans.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE PAYMENT PROVIDER & SESSION
        |--------------------------------------------------------------------------
        */
        $order->update([
            'payment_provider' => 'midtrans:' . $midtransOrderId,
        ]);

        session()->put(
            'midtrans_order_' . $order->id,
            [
                'midtrans_order_id'  => $midtransOrderId,
                'transaction_id'     => $response->transaction_id,
                'qr_string'          => $response->qr_string ?? null,
                'qr_code_url'        => $qrCodeUrl,
                'transaction_status' => $response->transaction_status ?? 'pending',
                'expiry_time'        => $response->expiry_time ?? null,
            ]
        );

        session()->forget('cart');

        return redirect()
            ->route('customer.order.success', [
                'code'        => $code,
                'orderNumber' => $encryptedOrderNumber,
            ])
            ->with(
                'success',
                'Pesanan berhasil dibuat. Silakan scan QRIS untuk membayar.'
            );
    }

    /**
     * Detail / Status Pesanan.
     */
    public function success(string $code, string $orderNumber)
    {
        // Dekripsi parameter orderNumber jika terenkripsi
        try {
            $decryptedOrderNumber = Crypt::decryptString($orderNumber);
        } catch (\Exception $e) {
            $decryptedOrderNumber = $orderNumber; // Fallback jika tidak terenkripsi
        }

        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        $merchant = $qrCode->merchant;

        $order = Order::with('items')
            ->where('order_number', $decryptedOrderNumber)
            ->where('merchant_id', $merchant->id)
            ->firstOrFail();

        $payment = session()->get('midtrans_order_' . $order->id);

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