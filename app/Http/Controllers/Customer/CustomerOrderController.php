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
     * =========================================================
     * MENU
     * =========================================================
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

        return view(
            'customer.menu',
            compact(
                'qrCode',
                'merchant',
                'categories',
                'menus'
            )
        );
    }


    /**
     * =========================================================
     * ADD TO CART
     * =========================================================
     */
    public function addToCart(
        Request $request,
        string $code
    ) {
        $request->validate([
            'menu_id' => [
                'required',
                'integer',
                'exists:menus,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:50',
            ],
        ], [
            'menu_id.required' =>
                'Menu tidak valid.',

            'quantity.required' =>
                'Jumlah pesanan wajib diisi.',

            'quantity.min' =>
                'Jumlah minimal pesanan adalah 1.',

            'quantity.max' =>
                'Jumlah pesanan melebihi batas per item.',
        ]);


        /*
        |---------------------------------------------------------
        | QR CODE
        |---------------------------------------------------------
        */

        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();


        /*
        |---------------------------------------------------------
        | MENU
        |---------------------------------------------------------
        */

        $menu = Menu::where(
                'id',
                $request->menu_id
            )
            ->where(
                'merchant_id',
                $qrCode->merchant_id
            )
            ->where(
                'status',
                'available'
            )
            ->firstOrFail();


        /*
        |---------------------------------------------------------
        | STOCK
        |---------------------------------------------------------
        */

        if ($menu->stock <= 0) {

            return response()->json([
                'success' => false,
                'message' =>
                    "Maaf, {$menu->name} sedang habis.",
            ], 422);
        }


        if ($request->quantity > $menu->stock) {

            return response()->json([
                'success' => false,
                'message' =>
                    "Maaf, stok {$menu->name} hanya tersisa {$menu->stock}.",
            ], 422);
        }


        /*
        |---------------------------------------------------------
        | CART
        |---------------------------------------------------------
        */

        $cart = session()->get(
            'cart',
            []
        );


        /*
        |---------------------------------------------------------
        | MENU SUDAH ADA
        |---------------------------------------------------------
        */

        if (isset($cart[$menu->id])) {

            $newQuantity =
                $cart[$menu->id]
                +
                $request->quantity;


            if ($newQuantity > $menu->stock) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        "Maaf, stok {$menu->name} hanya tersisa {$menu->stock}.",
                ], 422);
            }


            $cart[$menu->id] =
                $newQuantity;

        } else {

            $cart[$menu->id] =
                $request->quantity;
        }


        /*
        |---------------------------------------------------------
        | SIMPAN CART
        |---------------------------------------------------------
        */

        session()->put(
            'cart',
            $cart
        );


        return response()->json([
            'success' => true,

            'message' =>
                "{$menu->name} berhasil ditambahkan ke keranjang.",

            'cart_count' =>
                array_sum($cart),
        ]);
    }


    /**
     * =========================================================
     * CART
     * =========================================================
     */
    public function cart(string $code)
    {
        $qrCode = QrCode::where('code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        $merchant = $qrCode->merchant;

        $cart = session()->get(
            'cart',
            []
        );


        $menus = Menu::whereIn(
                'id',
                array_keys($cart)
            )
            ->where(
                'merchant_id',
                $merchant->id
            )
            ->where(
                'status',
                'available'
            )
            ->get()
            ->keyBy('id');


        $cartItems = [];


        foreach (
            $cart
            as $menuId => $quantity
        ) {

            if (!isset($menus[$menuId])) {
                continue;
            }


            $menu =
                $menus[$menuId];


            $cartItems[] = [

                'menu' =>
                    $menu,

                'quantity' =>
                    $quantity,

                'subtotal' =>
                    $menu->price * $quantity,
            ];
        }


        $total =
            collect($cartItems)
                ->sum('subtotal');


        return view(
            'customer.cart',
            compact(
                'qrCode',
                'merchant',
                'cartItems',
                'total'
            )
        );
    }


    /**
     * =========================================================
     * UPDATE CART
     * =========================================================
     */
    public function updateCart(
        Request $request,
        string $code
    ) {
        $request->validate([
            'menu_id' => [
                'required',
                'integer',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:0',
                'max:50',
            ],
        ]);


        $qrCode = QrCode::where(
                'code',
                $code
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();


        $menu = Menu::where(
                'id',
                $request->menu_id
            )
            ->where(
                'merchant_id',
                $qrCode->merchant_id
            )
            ->where(
                'status',
                'available'
            )
            ->firstOrFail();


        $cart =
            session()->get(
                'cart',
                []
            );


        /*
        |---------------------------------------------------------
        | HAPUS
        |---------------------------------------------------------
        */

        if ($request->quantity <= 0) {

            unset(
                $cart[$menu->id]
            );

        } else {

            /*
            |-----------------------------------------------------
            | CEK STOCK
            |-----------------------------------------------------
            */

            if (
                $request->quantity
                >
                $menu->stock
            ) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        "Maaf, stok {$menu->name} hanya tersisa {$menu->stock}.",
                ], 422);
            }


            $cart[$menu->id] =
                $request->quantity;
        }


        session()->put(
            'cart',
            $cart
        );


        /*
        |---------------------------------------------------------
        | HITUNG ULANG
        |---------------------------------------------------------
        */

        $items = [];


        foreach (
            $cart
            as $menuId => $quantity
        ) {

            $cartMenu = Menu::where(
                    'id',
                    $menuId
                )
                ->where(
                    'merchant_id',
                    $qrCode->merchant_id
                )
                ->where(
                    'status',
                    'available'
                )
                ->first();


            if (!$cartMenu) {
                continue;
            }


            $items[] = [

                'menu_id' =>
                    $cartMenu->id,

                'quantity' =>
                    $quantity,

                'subtotal' =>
                    $cartMenu->price * $quantity,
            ];
        }


        $total =
            collect($items)
                ->sum('subtotal');


        $currentItem =
            collect($items)
                ->firstWhere(
                    'menu_id',
                    $menu->id
                );


        return response()->json([

            'success' =>
                true,

            'quantity' =>
                $currentItem['quantity']
                ?? 0,

            'subtotal' =>
                $currentItem['subtotal']
                ?? 0,

            'total' =>
                $total,

            'removed' =>
                !isset($cart[$menu->id]),
        ]);
    }


    /**
     * =========================================================
     * REMOVE FROM CART
     * =========================================================
     */
    public function removeFromCart(
        string $code,
        int $menuId
    ) {
        $qrCode = QrCode::where(
                'code',
                $code
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();


        $menu = Menu::where(
                'id',
                $menuId
            )
            ->where(
                'merchant_id',
                $qrCode->merchant_id
            )
            ->firstOrFail();


        $cart =
            session()->get(
                'cart',
                []
            );


        unset(
            $cart[$menu->id]
        );


        session()->put(
            'cart',
            $cart
        );


        return back()->with(
            'success',
            "{$menu->name} dihapus dari keranjang."
        );
    }


    /**
     * =========================================================
     * CHECKOUT
     * =========================================================
     */
    public function checkout(string $code)
    {
        $qrCode = QrCode::where(
                'code',
                $code
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();


        $merchant =
            $qrCode->merchant;


        $cart =
            session()->get(
                'cart',
                []
            );


        if (empty($cart)) {

            return redirect()
                ->route(
                    'customer.cart',
                    $code
                )
                ->with(
                    'error',
                    'Keranjang masih kosong.'
                );
        }


        $menus = Menu::whereIn(
                'id',
                array_keys($cart)
            )
            ->where(
                'merchant_id',
                $merchant->id
            )
            ->where(
                'status',
                'available'
            )
            ->get()
            ->keyBy('id');


        $cartItems = [];


        foreach (
            $cart
            as $menuId => $quantity
        ) {

            if (!isset($menus[$menuId])) {
                continue;
            }


            $menu =
                $menus[$menuId];


            $cartItems[] = [

                'menu' =>
                    $menu,

                'quantity' =>
                    $quantity,

                'subtotal' =>
                    $menu->price * $quantity,
            ];
        }


        $total =
            collect($cartItems)
                ->sum('subtotal');


        /*
        |---------------------------------------------------------
        | DAFTAR BANK
        |---------------------------------------------------------
        */

        $banks = [

            'bca' =>
                'BCA',

            'bni' =>
                'BNI',

            'bri' =>
                'BRI',

            'permata' =>
                'Permata',

            'cimb' =>
                'CIMB',
        ];


        return view(
            'customer.checkout',
            compact(
                'qrCode',
                'merchant',
                'cartItems',
                'total',
                'banks'
            )
        );
    }


    /**
     * =========================================================
     * STORE / BUAT PESANAN
     * =========================================================
     */
    public function store(
        Request $request,
        string $code
    ) {

        /*
        |---------------------------------------------------------
        | VALIDASI INPUT
        |---------------------------------------------------------
        */

        $validated =
            $request->validate([

                'customer_name' => [
                    'required',
                    'string',
                    'max:50',
                    'regex:/^[a-zA-Z\s]+$/',
                ],

                'customer_phone' => [
                    'nullable',
                    'string',
                    'digits_between:10,14',
                ],

                'customer_email' => [
                    'nullable',
                    'email:dns',
                    'max:100',
                ],

                'payment_method' => [
                    'required',
                    'in:qris,cash,bank',
                ],

                'bank' => [
                    'nullable',
                    'required_if:payment_method,bank',
                    'in:bca,bni,bri,permata,cimb',
                ],

            ], [

                'customer_name.required' =>
                    'Nama pemesan wajib diisi.',

                'customer_name.regex' =>
                    'Nama pemesan hanya boleh berisi huruf dan spasi.',

                'customer_name.max' =>
                    'Nama pemesan maksimal 50 karakter.',

                'customer_phone.digits_between' =>
                    'Nomor telepon harus berupa angka antara 10-14 digit.',

                'customer_email.email' =>
                    'Format email tidak valid.',

                'payment_method.in' =>
                    'Metode pembayaran tidak valid.',

                'bank.required_if' =>
                    'Bank wajib dipilih untuk pembayaran transfer bank.',

                'bank.in' =>
                    'Bank yang dipilih tidak tersedia.',
            ]);


        /*
        |---------------------------------------------------------
        | QR CODE
        |---------------------------------------------------------
        */

        $qrCode = QrCode::where(
                'code',
                $code
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();


        $merchant =
            $qrCode->merchant;


        /*
        |---------------------------------------------------------
        | CART
        |---------------------------------------------------------
        */

        $cart =
            session()->get(
                'cart',
                []
            );


        if (empty($cart)) {

            return redirect()
                ->route(
                    'customer.cart',
                    $code
                )
                ->with(
                    'error',
                    'Keranjang masih kosong.'
                );
        }


        /*
        |---------------------------------------------------------
        | AMBIL MENU
        |---------------------------------------------------------
        */

        $menus = Menu::whereIn(
                'id',
                array_keys($cart)
            )
            ->where(
                'merchant_id',
                $merchant->id
            )
            ->where(
                'status',
                'available'
            )
            ->get()
            ->keyBy('id');


        if ($menus->isEmpty()) {

            return redirect()
                ->route(
                    'customer.cart',
                    $code
                )
                ->with(
                    'error',
                    'Menu dalam keranjang sudah tidak tersedia.'
                );
        }


        /*
        |---------------------------------------------------------
        | CEK STOCK + HITUNG SUBTOTAL
        |---------------------------------------------------------
        */

        $subtotal = 0;


        foreach (
            $cart
            as $menuId => $quantity
        ) {

            if (!isset($menus[$menuId])) {
                continue;
            }


            $menu =
                $menus[$menuId];


            if (
                $quantity
                >
                $menu->stock
            ) {

                return redirect()
                    ->route(
                        'customer.cart',
                        $code
                    )
                    ->with(
                        'error',
                        "Stok {$menu->name} tidak mencukupi."
                    );
            }


            $subtotal +=
                $menu->price
                *
                $quantity;
        }


        if ($subtotal <= 0) {

            return redirect()
                ->route(
                    'customer.cart',
                    $code
                )
                ->with(
                    'error',
                    'Total pesanan tidak valid.'
                );
        }


        /*
        |---------------------------------------------------------
        | ORDER NUMBER
        |---------------------------------------------------------
        */

        $orderNumber =
            'ORD-' .
            now()->format('YmdHis') .
            '-' .
            strtoupper(
                str()->random(6)
            );


        /*
        |---------------------------------------------------------
        | CREATE ORDER
        |---------------------------------------------------------
        */

        $order = DB::transaction(

            function () use (
                $validated,
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
                        strip_tags(
                            $validated['customer_name']
                        ),

                    'customer_phone' =>
                        $validated['customer_phone']
                        ?? null,

                    'customer_email' =>
                        $validated['customer_email']
                        ?? null,

                    'subtotal' =>
                        $subtotal,

                    'total' =>
                        $subtotal,

                    /*
                    |-------------------------------------------------
                    | PAYMENT
                    |-------------------------------------------------
                    */

                    'payment_method' =>
                        $validated['payment_method'],

                    'bank' =>
                        $validated['payment_method'] === 'bank'
                            ? $validated['bank']
                            : null,

                    'va_number' =>
                        null,

                    'payment_provider' =>
                        null,

                    'payment_status' =>
                        'pending',

                    'status' =>
                        'pending',
                ]);


                /*
                |-----------------------------------------------------
                | ORDER ITEMS
                |-----------------------------------------------------
                */

                foreach (
                    $cart
                    as $menuId => $quantity
                ) {

                    if (!isset($menus[$menuId])) {
                        continue;
                    }


                    $menu =
                        $menus[$menuId];


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
                            $menu->price
                            *
                            $quantity,
                    ]);
                }


                return $order;
            }
        );


        /*
        |---------------------------------------------------------
        | ENKRIPSI ORDER NUMBER
        |---------------------------------------------------------
        */

        $encryptedOrderNumber =
            Crypt::encryptString(
                $order->order_number
            );


        /*
        |---------------------------------------------------------
        | CASH
        |---------------------------------------------------------
        */

        if (
            $validated['payment_method']
            === 'cash'
        ) {

            $order->update([

                'payment_status' =>
                    'paid',
            ]);


            session()->forget(
                'cart'
            );


            return redirect()
                ->route(
                    'customer.order.success',
                    [
                        'code' =>
                            $code,

                        'orderNumber' =>
                            $encryptedOrderNumber,
                    ]
                )
                ->with(
                    'success',
                    'Pesanan berhasil dibuat.'
                );
        }


        /*
        |---------------------------------------------------------
        | MIDTRANS CONFIG
        |---------------------------------------------------------
        */

        Config::$serverKey =
            config(
                'services.midtrans.server_key'
            );

        Config::$clientKey =
            config(
                'services.midtrans.client_key'
            );

        Config::$isProduction =
            config(
                'services.midtrans.is_production',
                false
            );

        Config::$isSanitized =
            true;

        Config::$is3ds =
            true;

            /*
|--------------------------------------------------------------------------
| MIDTRANS CURL OPTIONS
|--------------------------------------------------------------------------
|
| HTTPHEADER harus ada karena library Midtrans membaca
| Config::$curlOptions[CURLOPT_HTTPHEADER].
|
*/

Config::$curlOptions = [
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => [],
];


        /*
        |---------------------------------------------------------
        | SERVER KEY CHECK
        |---------------------------------------------------------
        */

        if (
            empty(
                Config::$serverKey
            )
        ) {

            Log::error(
                'MIDTRANS SERVER KEY KOSONG'
            );


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Konfigurasi pembayaran belum tersedia.'
                );
        }


        /*
        |---------------------------------------------------------
        | MIDTRANS ORDER ID
        |---------------------------------------------------------
        */

        $midtransOrderId =
            'ORD-' .
            $order->id .
            '-' .
            now()->format('YmdHis') .
            '-' .
            strtoupper(
                str()->random(6)
            );


        /*
        |---------------------------------------------------------
        | ITEM DETAILS
        |---------------------------------------------------------
        */

        $itemDetails = [];


        foreach (
            $cart
            as $menuId => $quantity
        ) {

            if (!isset($menus[$menuId])) {
                continue;
            }


            $menu =
                $menus[$menuId];


            $itemDetails[] = [

                'id' =>
                    'MENU-' .
                    $menu->id,

                'price' =>
                    (int) $menu->price,

                'quantity' =>
                    (int) $quantity,

                'name' =>
                    $menu->name,
            ];
        }


        /*
        |---------------------------------------------------------
        | CUSTOMER DETAILS
        |---------------------------------------------------------
        */

        $customerDetails = [

            'first_name' =>
                $order->customer_name,
        ];


        if (
            !empty(
                $order->customer_email
            )
        ) {

            $customerDetails['email'] =
                $order->customer_email;
        }


        if (
            !empty(
                $order->customer_phone
            )
        ) {

            $customerDetails['phone'] =
                $order->customer_phone;
        }


        /*
        |---------------------------------------------------------
        | BASE MIDTRANS PARAMETER
        |---------------------------------------------------------
        */

        $params = [

            'transaction_details' => [

                'order_id' =>
                    $midtransOrderId,

                'gross_amount' =>
                    (int) $order->total,
            ],

            'item_details' =>
                $itemDetails,

            'customer_details' =>
                $customerDetails,
        ];


        /*
        |---------------------------------------------------------
        | QRIS
        |---------------------------------------------------------
        */

        if (
            $validated['payment_method']
            === 'qris'
        ) {

            $params['payment_type'] =
                'qris';
        }


        /*
        |---------------------------------------------------------
        | BANK TRANSFER
        |---------------------------------------------------------
        */

        elseif (
            $validated['payment_method']
            === 'bank'
        ) {

            $params['payment_type'] =
                'bank_transfer';


            $params['bank_transfer'] = [

                'bank' =>
                    $validated['bank'],
            ];
        }


        /*
        |---------------------------------------------------------
        | LOG REQUEST
        |---------------------------------------------------------
        */

        Log::info(
            'MIDTRANS CHARGE REQUEST',
            [

                'order_id' =>
                    $order->id,

                'midtrans_order_id' =>
                    $midtransOrderId,

                'payment_method' =>
                    $validated['payment_method'],

                'bank' =>
                    $validated['bank']
                    ?? null,

                'gross_amount' =>
                    $order->total,
            ]
        );


        /*
        |---------------------------------------------------------
        | CHARGE MIDTRANS
        |---------------------------------------------------------
        */

        try {

            $response =
                CoreApi::charge(
                    $params
                );


            Log::info(
                'MIDTRANS CHARGE RESPONSE',
                [

                    'order_id' =>
                        $order->id,

                    'midtrans_order_id' =>
                        $midtransOrderId,

                    'payment_method' =>
                        $validated['payment_method'],

                    'bank' =>
                        $validated['bank']
                        ?? null,

                    'response' =>
                        json_decode(
                            json_encode(
                                $response
                            ),
                            true
                        ),
                ]
            );

        } catch (Throwable $e) {

            Log::error(
                'MIDTRANS CHARGE ERROR',
                [

                    'order_id' =>
                        $order->id,

                    'midtrans_order_id' =>
                        $midtransOrderId,

                    'payment_method' =>
                        $validated['payment_method'],

                    'bank' =>
                        $validated['bank']
                        ?? null,

                    'message' =>
                        $e->getMessage(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal membuat pembayaran. Silakan coba lagi.'
                );
        }


        /*
        |---------------------------------------------------------
        | TRANSACTION ID
        |---------------------------------------------------------
        */

        $transactionId =
            $response->transaction_id
            ?? null;


        $transactionStatus =
            $response->transaction_status
            ?? 'pending';


        if (
            empty(
                $transactionId
            )
        ) {

            Log::error(
                'MIDTRANS TRANSACTION ID TIDAK DITEMUKAN',
                [

                    'order_id' =>
                        $order->id,

                    'response' =>
                        json_encode(
                            $response
                        ),
                ]
            );


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Transaksi pembayaran gagal dibuat.'
                );
        }


        /*
        |---------------------------------------------------------
        | VA NUMBER
        |---------------------------------------------------------
        */

        $vaNumber = null;


        if (
            $validated['payment_method']
            === 'bank'
        ) {

            /*
            |-----------------------------------------------------
            | BCA / BNI / BRI / CIMB
            |-----------------------------------------------------
            */

            if (
                !empty(
                    $response->va_numbers
                )
            ) {

                foreach (
                    $response->va_numbers
                    as $va
                ) {

                    /*
                    |-------------------------------------------------
                    | Pastikan VA sesuai bank yang dipilih
                    |-------------------------------------------------
                    */

                    if (
                        isset(
                            $va->bank
                        )
                        &&
                        strtolower(
                            $va->bank
                        )
                        ===
                        strtolower(
                            $validated['bank']
                        )
                    ) {

                        $vaNumber =
                            $va->va_number
                            ?? null;

                        break;
                    }
                }


                /*
                |-----------------------------------------------------
                | FALLBACK
                |-----------------------------------------------------
                */

                if (
                    empty(
                        $vaNumber
                    )
                    &&
                    isset(
                        $response->va_numbers[0]
                    )
                ) {

                    $vaNumber =
                        $response
                            ->va_numbers[0]
                            ->va_number
                            ?? null;
                }
            }


            /*
            |-----------------------------------------------------
            | PERMATA
            |-----------------------------------------------------
            */

            if (
                $validated['bank']
                === 'permata'
                &&
                empty(
                    $vaNumber
                )
            ) {

                $vaNumber =
                    $response->permata_va_number
                    ?? null;
            }


            /*
            |-----------------------------------------------------
            | VA TIDAK DITEMUKAN
            |-----------------------------------------------------
            */

            if (
                empty(
                    $vaNumber
                )
            ) {

                Log::error(
                    'MIDTRANS VA NUMBER TIDAK DITEMUKAN',
                    [

                        'order_id' =>
                            $order->id,

                        'bank' =>
                            $validated['bank'],

                        'response' =>
                            json_encode(
                                $response
                            ),
                    ]
                );


                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Nomor Virtual Account gagal dibuat.'
                    );
            }
        }


        /*
        |---------------------------------------------------------
        | QRIS URL
        |---------------------------------------------------------
        */

        $qrCodeUrl = null;


        if (
            $validated['payment_method']
            === 'qris'
        ) {

            $midtransBaseUrl =
                Config::$isProduction
                    ? 'https://api.midtrans.com'
                    : 'https://api.sandbox.midtrans.com';


            $qrCodeUrl =
                $midtransBaseUrl .
                '/v2/qris/' .
                $transactionId .
                '/qr-code';
        }


        /*
        |---------------------------------------------------------
        | UPDATE ORDER
        |---------------------------------------------------------
        */

        $order->update([

            'payment_provider' =>
                'midtrans:' .
                $midtransOrderId,

            'bank' =>
                $validated['payment_method']
                === 'bank'
                    ? $validated['bank']
                    : null,

            'va_number' =>
                $vaNumber,

            'payment_status' =>
                'pending',
        ]);


        /*
        |---------------------------------------------------------
        | PAYMENT SESSION
        |---------------------------------------------------------
        */

        $paymentData = [

            'midtrans_order_id' =>
                $midtransOrderId,

            'transaction_id' =>
                $transactionId,

            'transaction_status' =>
                $transactionStatus,

            'expiry_time' =>
                $response->expiry_time
                ?? null,

            'payment_method' =>
                $validated['payment_method'],

            'bank' =>
                $validated['bank']
                ?? null,

            'va_number' =>
                $vaNumber,

            'qr_code_url' =>
                $qrCodeUrl,

            'qr_string' =>
                $response->qr_string
                ?? null,
        ];


        session()->put(
            'midtrans_order_' .
            $order->id,
            $paymentData
        );


        /*
        |---------------------------------------------------------
        | HAPUS CART
        |---------------------------------------------------------
        */

        session()->forget(
            'cart'
        );


        /*
        |---------------------------------------------------------
        | REDIRECT
        |---------------------------------------------------------
        */

        return redirect()
            ->route(
                'customer.order.success',
                [

                    'code' =>
                        $code,

                    'orderNumber' =>
                        $encryptedOrderNumber,
                ]
            )
            ->with(
                'success',

                $validated['payment_method']
                === 'bank'

                    ? 'Pesanan berhasil dibuat. Silakan lakukan pembayaran melalui Virtual Account.'

                    : 'Pesanan berhasil dibuat. Silakan scan QRIS untuk membayar.'
            );
    }


    /**
     * =========================================================
     * ORDER SUCCESS
     * =========================================================
     */
    public function success(
        string $code,
        string $orderNumber
    ) {

        /*
        |---------------------------------------------------------
        | DEKRIPSI ORDER NUMBER
        |---------------------------------------------------------
        */

        try {

            $decryptedOrderNumber =
                Crypt::decryptString(
                    $orderNumber
                );

        } catch (\Exception $e) {

            $decryptedOrderNumber =
                $orderNumber;
        }


        /*
        |---------------------------------------------------------
        | QR CODE
        |---------------------------------------------------------
        */

        $qrCode = QrCode::where(
                'code',
                $code
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();


        $merchant =
            $qrCode->merchant;


        /*
        |---------------------------------------------------------
        | ORDER
        |---------------------------------------------------------
        */

        $order = Order::with(
                'items'
            )
            ->where(
                'order_number',
                $decryptedOrderNumber
            )
            ->where(
                'merchant_id',
                $merchant->id
            )
            ->firstOrFail();


        /*
        |---------------------------------------------------------
        | PAYMENT DATA
        |---------------------------------------------------------
        */

        $payment =
            session()->get(
                'midtrans_order_' .
                $order->id
            );


        if (!is_array($payment)) {

            $payment = [];
        }


        /*
        |---------------------------------------------------------
        | FALLBACK DATA DARI DATABASE
        |---------------------------------------------------------
        */

        if (
            $order->payment_method
            === 'bank'
        ) {

            $payment['payment_method'] =
                'bank';

            $payment['bank'] =
                $order->bank;

            $payment['va_number'] =
                $order->va_number;
        }


        /*
        |---------------------------------------------------------
        | QRIS URL
        |---------------------------------------------------------
        */

        if (
            $order->payment_method
            === 'qris'
            &&
            !empty(
                $payment['transaction_id']
            )
        ) {

            $midtransBaseUrl =
                Config::$isProduction
                    ? 'https://api.midtrans.com'
                    : 'https://api.sandbox.midtrans.com';


            $payment['qr_code_url'] =
                $midtransBaseUrl .
                '/v2/qris/' .
                $payment['transaction_id'] .
                '/qr-code';
        }


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


    /**
     * =========================================================
     * PAYMENT STATUS
     * =========================================================
     *
     * Digunakan untuk polling status pembayaran.
     */
    public function payment(
        string $code,
        string $orderNumber
    ) {

        /*
        |---------------------------------------------------------
        | DEKRIPSI
        |---------------------------------------------------------
        */

        try {

            $decryptedOrderNumber =
                Crypt::decryptString(
                    $orderNumber
                );

        } catch (\Exception $e) {

            $decryptedOrderNumber =
                $orderNumber;
        }


        /*
        |---------------------------------------------------------
        | QR CODE
        |---------------------------------------------------------
        */

        $qrCode = QrCode::where(
                'code',
                $code
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();


        /*
        |---------------------------------------------------------
        | ORDER
        |---------------------------------------------------------
        */

        $order = Order::where(
                'order_number',
                $decryptedOrderNumber
            )
            ->where(
                'merchant_id',
                $qrCode->merchant_id
            )
            ->firstOrFail();


        /*
        |---------------------------------------------------------
        | RESPONSE
        |---------------------------------------------------------
        */

        return response()->json([

            'success' =>
                true,

            'payment_status' =>
                $order->payment_status,

            'status' =>
                $order->status,

            'is_paid' =>
                $order->payment_status
                === 'paid',

            'is_failed' =>
                in_array(
                    $order->payment_status,
                    [
                        'failed',
                        'expired',
                    ]
                ),

            'is_pending' =>
                $order->payment_status
                === 'pending',

            'bank' =>
                $order->bank,

            'va_number' =>
                $order->va_number,
        ]);
    }
}