<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Snap;
use Midtrans\Transaction;
use Throwable;

class PaymentController extends Controller
{
    /**
     * Konfigurasi Midtrans.
     */
    private function configureMidtrans(): void
    {
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
    }


    /**
     * Mengambil subscription berdasarkan user yang sedang login.
     */
    private function getSubscription(
        string $encryptedSubscription
    ) {
        $subscriptionId = decryptId(
            $encryptedSubscription
        );

        if (!$subscriptionId) {
            return [
                'error' => redirect()
                    ->route('public.subscription.index')
                    ->with(
                        'error',
                        'ID subscription tidak valid.'
                    ),
            ];
        }

        $user = Auth::user();

        if (!$user) {
            return [
                'error' => redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'Silakan login terlebih dahulu.'
                    ),
            ];
        }

        if (!$user->merchant_id) {
            return [
                'error' => redirect()
                    ->route('merchant.setup')
                    ->with(
                        'error',
                        'Data toko belum tersedia.'
                    ),
            ];
        }

        $subscription = Subscription::with([
            'merchant',
            'packageDuration.package',
        ])
            ->where('id', $subscriptionId)
            ->where(
                'merchant_id',
                $user->merchant_id
            )
            ->first();

        if (!$subscription) {
            return [
                'error' => redirect()
                    ->route('public.subscription.index')
                    ->with(
                        'error',
                        'Data subscription tidak ditemukan.'
                    ),
            ];
        }

        return [
            'user' => $user,
            'subscription' => $subscription,
        ];
    }


    /**
     * Membuat Order ID Midtrans.
     */
    private function generateOrderId(
        Subscription $subscription
    ): string {
        return
            'SUB-' .
            $subscription->id .
            '-' .
            now()->format('YmdHis') .
            '-' .
            strtoupper(
                str()->random(6)
            );
    }


    /**
     * Detail item subscription.
     */
    private function getItemDetails(
        Subscription $subscription
    ): array {
        return [
            [
                'id' =>
                    'SUB-' .
                    $subscription->id,

                'price' =>
                    (int) $subscription->price,

                'quantity' => 1,

                'name' =>
                    'Langganan ' .
                    $subscription->packageDuration
                        ->package
                        ->name .
                    ' - ' .
                    $subscription->packageDuration
                        ->name,
            ],
        ];
    }


    public function method(string $encryptedSubscription)
    {
        $data = $this->getSubscription($encryptedSubscription);

        if (isset($data['error'])) {
            return $data['error'];
        }

        $subscription = $data['subscription'];

        if ($subscription->status === 'active') {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Subscription Anda sudah aktif.');
        }

        if ($subscription->status !== 'pending') {
            return redirect()
                ->route('public.subscription.index')
                ->with('error', 'Subscription ini tidak dapat dibayar.');
        }

        return view(
            'public_subscription.payment_method',
            compact('subscription')
        );
    }


    public function show(
        string $encryptedSubscription
    ) {
        $data = $this->getSubscription(
            $encryptedSubscription
        );

        if (isset($data['error'])) {
            return $data['error'];
        }

        $subscription = $data['subscription'];

        if ($subscription->status === 'active') {
            return redirect()
                ->route('dashboard')
                ->with(
                    'info',
                    'Subscription Anda sudah aktif.'
                );
        }

        if ($subscription->status !== 'pending') {
            return redirect()
                ->route('public.subscription.index')
                ->with(
                    'error',
                    'Subscription ini tidak dapat dibayar.'
                );
        }

        return view(
            'payment.show',
            compact('subscription')
        );
    }


    public function qris(
        string $encryptedSubscription
    ) {
        $data = $this->getSubscription(
            $encryptedSubscription
        );

        if (isset($data['error'])) {
            return $data['error'];
        }

        $user = $data['user'];
        $subscription = $data['subscription'];


        /*
        |--------------------------------------------------------------------------
        | Pastikan Subscription Bisa Dibayar
        |--------------------------------------------------------------------------
        */

        if ($subscription->status === 'active') {

            return redirect()
                ->route('dashboard')
                ->with(
                    'info',
                    'Subscription Anda sudah aktif.'
                );
        }


        if ($subscription->status !== 'pending') {

            return redirect()
                ->route('public.subscription.index')
                ->with(
                    'error',
                    'Subscription ini tidak dapat dibayar.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Konfigurasi Midtrans
        |--------------------------------------------------------------------------
        */

        $this->configureMidtrans();


        if (empty(Config::$serverKey)) {

            Log::error(
                'Midtrans Server Key belum dikonfigurasi.'
            );

            return back()->with(
                'error',
                'Konfigurasi Midtrans belum lengkap.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Generate Order ID
        |--------------------------------------------------------------------------
        */

        $orderId =
            $this->generateOrderId(
                $subscription
            );


        /*
        |--------------------------------------------------------------------------
        | Parameter QRIS
        |--------------------------------------------------------------------------
        */

        $params = [

            'payment_type' => 'qris',

            'transaction_details' => [

                'order_id' =>
                    $orderId,

                'gross_amount' =>
                    (int) $subscription->price,

            ],

            'item_details' =>
                $this->getItemDetails(
                    $subscription
                ),

            'customer_details' => [

                'first_name' =>
                    $user->name,

                'email' =>
                    $user->email,

            ],

            'qris' => [

                'acquirer' => 'gopay',

            ],

        ];


        /*
        |--------------------------------------------------------------------------
        | Generate QRIS
        |--------------------------------------------------------------------------
        */

        try {

            $response =
                CoreApi::charge(
                    $params
                );

        } catch (Throwable $e) {

            Log::error(
                'Midtrans QRIS Error',
                [

                    'subscription_id' =>
                        $subscription->id,

                    'order_id' =>
                        $orderId,

                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),

                ]
            );

            return back()->with(
                'error',
                'Gagal membuat pembayaran QRIS.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil QR URL
        |--------------------------------------------------------------------------
        */

        $qrUrl = null;

        foreach (
            ($response->actions ?? [])
            as $action
        ) {

            if (
                ($action->name ?? null)
                === 'generate-qr-code'
            ) {

                $qrUrl =
                    $action->url;

                break;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | QR URL Tidak Ditemukan
        |--------------------------------------------------------------------------
        */

        if (!$qrUrl) {

            Log::error(
                'QR URL Midtrans tidak ditemukan.',
                [

                    'subscription_id' =>
                        $subscription->id,

                    'order_id' =>
                        $orderId,

                    'response' =>
                        $response,

                ]
            );

            return back()->with(
                'error',
                'QRIS berhasil dibuat tetapi QR Code tidak dapat ditampilkan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan Order ID
        |--------------------------------------------------------------------------
        |
        | Digunakan oleh qrisStatus()
        | untuk mengecek transaksi Midtrans.
        |
        */

        $subscription->update([

            'invoice_number' =>
                $orderId,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Tampilkan Halaman QRIS Custom
        |--------------------------------------------------------------------------
        */

        return view(
            'payment.qris',
            compact(
                'subscription',
                'orderId',
                'qrUrl'
            )
        );
    }


    public function qrisStatus(
        string $encryptedSubscription
    ) {
        /*
        |--------------------------------------------------------------------------
        | Ambil Subscription
        |--------------------------------------------------------------------------
        */

        $data = $this->getSubscription(
            $encryptedSubscription
        );


        /*
        |--------------------------------------------------------------------------
        | Subscription Tidak Valid
        |--------------------------------------------------------------------------
        */

        if (isset($data['error'])) {

            return response()->json(
                [
                    'success' => false,
                    'paid' => false,
                    'message' =>
                        'Subscription tidak valid.',
                ],
                404
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil Data User & Subscription
        |--------------------------------------------------------------------------
        */

        $user =
            $data['user'];

        $subscription =
            $data['subscription'];


        /*
        |--------------------------------------------------------------------------
        | Validasi Merchant
        |--------------------------------------------------------------------------
        */

        if (!$user->merchant_id) {

            return response()->json(
                [
                    'success' => false,
                    'paid' => false,
                    'message' =>
                        'Data merchant tidak ditemukan.',
                ],
                401
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Jika Subscription Sudah Active
        |--------------------------------------------------------------------------
        |
        | Ini penting ketika webhook Midtrans sudah lebih dulu
        | mengubah subscription menjadi active.
        |
        */

        if (
            $subscription->status === 'active'
        ) {

            return response()->json(
                [

                    'success' =>
                        true,

                    'paid' =>
                        true,

                    'status' =>
                        'settlement',

                    'message' =>
                        'Pembayaran sudah berhasil.',

                    'redirect' =>
                        route('dashboard'),

                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan Invoice / Order ID Ada
        |--------------------------------------------------------------------------
        */

        if (
            empty(
                $subscription->invoice_number
            )
        ) {

            return response()->json(
                [

                    'success' =>
                        true,

                    'paid' =>
                        false,

                    'status' =>
                        'pending',

                    'message' =>
                        'Order ID pembayaran belum tersedia.',

                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Konfigurasi Midtrans
        |--------------------------------------------------------------------------
        */

        $this->configureMidtrans();


        try {

            /*
            |--------------------------------------------------------------------------
            | Order ID
            |--------------------------------------------------------------------------
            */

            $orderId =
                $subscription->invoice_number;


            /*
            |--------------------------------------------------------------------------
            | Cek Status Transaksi Midtrans
            |--------------------------------------------------------------------------
            */

            $status =
                Transaction::status(
                    $orderId
                );


            /*
            |--------------------------------------------------------------------------
            | Ambil Transaction Status
            |--------------------------------------------------------------------------
            */

            $transactionStatus =
                $status->transaction_status
                ?? 'unknown';


            /*
            |--------------------------------------------------------------------------
            | Pembayaran Berhasil
            |--------------------------------------------------------------------------
            */

            $paymentSuccess =
                false;


            /*
            |--------------------------------------------------------------------------
            | Settlement
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'settlement'
            ) {

                $paymentSuccess =
                    true;
            }


            /*
            |--------------------------------------------------------------------------
            | Capture + Fraud Accept
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'capture'
                &&
                ($status->fraud_status ?? null)
                    === 'accept'
            ) {

                $paymentSuccess =
                    true;
            }


            /*
            |--------------------------------------------------------------------------
            | Jika Pembayaran Berhasil
            |--------------------------------------------------------------------------
            */

            if ($paymentSuccess) {


                /*
                |--------------------------------------------------------------------------
                | Aktifkan Subscription
                |--------------------------------------------------------------------------
                */

                if (
                    $subscription->status
                    !== 'active'
                ) {

                    $startDate =
                        today();


                    $durationDays =
                        (int) $subscription
                            ->packageDuration
                            ->duration_days;


                    /*
                    |--------------------------------------------------------------------------
                    | Hitung End Date
                    |--------------------------------------------------------------------------
                    |
                    | Hari pembayaran dihitung sebagai hari pertama.
                    |
                    */

                    $endDate =
                        $startDate->copy()
                            ->addDays(
                                max(
                                    0,
                                    $durationDays - 1
                                )
                            );


                    $subscription->update([

                        'start_date' =>
                            $startDate,

                        'end_date' =>
                            $endDate,

                        'paid_at' =>
                            now(),

                        'status' =>
                            'active',

                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Refresh Data
                    |--------------------------------------------------------------------------
                    */

                    $subscription->refresh();
                }


                /*
                |--------------------------------------------------------------------------
                | Response Berhasil
                |--------------------------------------------------------------------------
                */

                return response()->json(
                    [

                        'success' =>
                            true,

                        'paid' =>
                            true,

                        'status' =>
                            'settlement',

                        'message' =>
                            'Pembayaran berhasil.',

                        'redirect' =>
                            route('dashboard'),

                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Status Pending
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'pending'
            ) {

                return response()->json(
                    [

                        'success' =>
                            true,

                        'paid' =>
                            false,

                        'status' =>
                            'pending',

                        'message' =>
                            'Menunggu pembayaran.',

                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Expired
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'expire'
            ) {

                return response()->json(
                    [

                        'success' =>
                            true,

                        'paid' =>
                            false,

                        'status' =>
                            'expire',

                        'message' =>
                            'Pembayaran telah kedaluwarsa.',

                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Cancel
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'cancel'
            ) {

                return response()->json(
                    [

                        'success' =>
                            true,

                        'paid' =>
                            false,

                        'status' =>
                            'cancel',

                        'message' =>
                            'Pembayaran dibatalkan.',

                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Deny
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'deny'
            ) {

                return response()->json(
                    [

                        'success' =>
                            true,

                        'paid' =>
                            false,

                        'status' =>
                            'deny',

                        'message' =>
                            'Pembayaran ditolak.',

                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Status Lain
            |--------------------------------------------------------------------------
            */

            return response()->json(
                [

                    'success' =>
                        true,

                    'paid' =>
                        false,

                    'status' =>
                        $transactionStatus,

                    'message' =>
                        'Pembayaran belum selesai.',

                ]
            );


        } catch (Throwable $e) {


            /*
            |--------------------------------------------------------------------------
            | Log Error
            |--------------------------------------------------------------------------
            */

            Log::error(
                'QRIS Status Check Error',
                [

                    'subscription_id' =>
                        $subscription->id,

                    'order_id' =>
                        $subscription->invoice_number,

                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),

                ]
            );


            /*
            |--------------------------------------------------------------------------
            | Response Error
            |--------------------------------------------------------------------------
            */

            return response()->json(
                [

                    'success' =>
                        false,

                    'paid' =>
                        false,

                    'message' =>
                        'Gagal mengecek status pembayaran.',

                ],
                500
            );
        }
    }


    public function bankStatus(
        string $encryptedSubscription
    ) {

        /*
        |--------------------------------------------------------------------------
        | Ambil Subscription
        |--------------------------------------------------------------------------
        */

        $data = $this->getSubscription(
            $encryptedSubscription
        );


        if (isset($data['error'])) {

            return response()->json([

                'success' => false,
                'paid' => false,
                'message' => 'Subscription tidak valid.'

            ], 404);

        }


        $subscription = $data['subscription'];


        /*
        |--------------------------------------------------------------------------
        | Jika sudah aktif
        |--------------------------------------------------------------------------
        */

        if ($subscription->status === 'active') {

            return response()->json([

                'success' => true,

                'paid' => true,

                'status' => 'settlement',

                'redirect' => route('dashboard')

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan Invoice Ada
        |--------------------------------------------------------------------------
        */

        if (!$subscription->invoice_number) {

            return response()->json([

                'success' => true,

                'paid' => false,

                'status' => 'pending',

                'message' => 'Invoice belum tersedia.'

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Cek Midtrans
        |--------------------------------------------------------------------------
        */

        $this->configureMidtrans();


        try {


            $status = Transaction::status(
                $subscription->invoice_number
            );


            $transactionStatus =
                $status->transaction_status
                ?? 'unknown';


            /*
            |--------------------------------------------------------------------------
            | Pembayaran berhasil
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'settlement'
            ) {


                if ($subscription->status !== 'active') {


                    $startDate = today();


                    $durationDays =
                        (int)
                        $subscription
                            ->packageDuration
                            ->duration_days;


                    $endDate =
                        $startDate->copy()
                            ->addDays(
                                max(
                                    0,
                                    $durationDays - 1
                                )
                            );


                    $subscription->update([

                        'start_date' => $startDate,

                        'end_date' => $endDate,

                        'paid_at' => now(),

                        'status' => 'active',

                        'payment_status' => 'paid',

                    ]);

                }


                return response()->json([

                    'success' => true,

                    'paid' => true,

                    'status' => 'settlement',

                    'message' =>
                        'Pembayaran berhasil.',

                    'redirect' =>
                        route('dashboard'),

                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | Pending
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'pending'
            ) {

                return response()->json([

                    'success' => true,

                    'paid' => false,

                    'status' => 'pending',

                    'message' =>
                        'Menunggu pembayaran.'

                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | Expired
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'expire'
            ) {


                $subscription->update([

                    'payment_status' =>
                        'expired'

                ]);


                return response()->json([

                    'success' => true,

                    'paid' => false,

                    'status' => 'expire',

                    'message' =>
                        'Pembayaran expired.'

                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | Status lainnya
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'paid' => false,

                'status' =>
                    $transactionStatus,

            ]);


        } catch (Throwable $e) {


            Log::error(
                'Bank Status Check Error',
                [

                    'subscription_id'
                        => $subscription->id,

                    'order_id'
                        => $subscription->invoice_number,

                    'message'
                        => $e->getMessage(),

                ]
            );


            return response()->json([

                'success' => false,

                'paid' => false,

                'message' =>
                    'Gagal mengecek pembayaran.'

            ], 500);


        }

    }


    /**
     * Redirect ke halaman pembayaran default.
     */
    public function process(
        Request $request,
        string $encryptedSubscription
    ) {
        return redirect()->route(
            'public.subscription.payment',
            $encryptedSubscription
        );
    }


    /**
     * Halaman pilihan bank transfer.
     */
    public function bank(
        string $encryptedSubscription
    ) {
        $data = $this->getSubscription($encryptedSubscription);

        if (isset($data['error'])) {
            return $data['error'];
        }

        $subscription = $data['subscription'];

        if ($subscription->status === 'active') {
            return redirect()
                ->route('dashboard')
                ->with(
                    'info',
                    'Subscription Anda sudah aktif.'
                );
        }

        if ($subscription->status !== 'pending') {
            return redirect()
                ->route('public.subscription.index')
                ->with(
                    'error',
                    'Subscription ini tidak dapat dibayar.'
                );
        }

        return view(
            'public_subscription.payment_method',
            compact('subscription')
        );
    }


    /**
     * Membuat pembayaran Transfer Bank.
     */
    public function createBankPayment(
        Request $request,
        string $encryptedSubscription
    ) {
        $data = $this->getSubscription($encryptedSubscription);

        if (isset($data['error'])) {
            return $data['error'];
        }

        $user = $data['user'];
        $subscription = $data['subscription'];

        if ($subscription->status === 'active') {
            return redirect()
                ->route('dashboard')
                ->with(
                    'info',
                    'Subscription Anda sudah aktif.'
                );
        }

        if ($subscription->status !== 'pending') {
            return redirect()
                ->route('public.subscription.index')
                ->with(
                    'error',
                    'Subscription ini tidak dapat dibayar.'
                );
        }

        $request->validate([
            'bank' => [
                'required',
                'in:bca,bni,bri,mandiri,permata,cimb',
            ],
        ]);

        $this->configureMidtrans();

        $orderId = $this->generateOrderId($subscription);

        $params = [
            'payment_type' => 'bank_transfer',

            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $subscription->price,
            ],

            'bank_transfer' => [
                'bank' => $request->bank,
            ],

            'item_details' => $this->getItemDetails(
                $subscription
            ),

            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ];

        try {
            $response = CoreApi::charge($params);

        } catch (Throwable $e) {

            Log::error(
                'Midtrans Bank Transfer Error',
                [
                    'subscription_id' => $subscription->id,
                    'bank' => $request->bank,
                    'message' => $e->getMessage(),
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal membuat pembayaran.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil Virtual Account
        |--------------------------------------------------------------------------
        */

        $vaNumber = null;
        $bank = null;

        if (isset($response->va_numbers[0])) {

            $vaNumber =
                $response->va_numbers[0]->va_number
                ?? null;

            $bank =
                $response->va_numbers[0]->bank
                ?? null;
        }


        /*
        |--------------------------------------------------------------------------
        | Fallback Permata
        |--------------------------------------------------------------------------
        */

        if (
            !$vaNumber &&
            isset($response->permata_va_number)
        ) {

            $vaNumber =
                $response->permata_va_number;

            $bank = 'permata';
        }


        /*
        |--------------------------------------------------------------------------
        | Fallback Mandiri
        |--------------------------------------------------------------------------
        */

        if (
            !$vaNumber &&
            isset($response->bill_key) &&
            isset($response->biller_code)
        ) {

            $vaNumber =
                $response->biller_code .
                $response->bill_key;

            $bank = 'mandiri';
        }


        /*
        |--------------------------------------------------------------------------
        | Fallback Bank
        |--------------------------------------------------------------------------
        */

        if (!$bank) {

            $bank =
                $request->bank;
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan Data Pembayaran
        |--------------------------------------------------------------------------
        */

        $subscription->update([
            'invoice_number' => $orderId,
            'payment_type' => 'bank_transfer',
            'payment_bank' => $bank,
            'va_number' => $vaNumber,
            'expired_at' => $response->expiry_time ?? null,
            'payment_status' => 'pending',
        ]);


        return view(
            'public_subscription.bank',
            compact('subscription')
        );
    }
}
