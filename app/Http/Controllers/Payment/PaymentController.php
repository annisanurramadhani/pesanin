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


    /**
     * ---------------------------------------------------------------
     * DEFAULT PAYMENT
     * ---------------------------------------------------------------
     *
     * Pembayaran utama menggunakan Midtrans Snap.
     *
     * User dapat memilih metode pembayaran
     * yang tersedia di Midtrans Snap.
     */
    public function show(
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
        | Parameter Snap
        |--------------------------------------------------------------------------
        */

        $params = [

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

        ];


        /*
        |--------------------------------------------------------------------------
        | Generate Snap Token
        |--------------------------------------------------------------------------
        */

        try {

            $snapToken =
                Snap::getSnapToken(
                    $params
                );

        } catch (Throwable $e) {

            Log::error(
                'Midtrans Snap Token Error',
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
                'Gagal membuat pembayaran Midtrans.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Tampilkan Payment Snap
        |--------------------------------------------------------------------------
        */

        return view(
            'payment.show',
            compact(
                'subscription',
                'snapToken',
                'orderId'
            )
        );
    }


    /**
     * ---------------------------------------------------------------
     * QRIS PAYMENT
     * ---------------------------------------------------------------
     *
     * QRIS dibuat melalui Midtrans Core API.
     *
     * Fungsi ini hanya dipanggil ketika user
     * memilih pembayaran QRIS custom.
     */
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


    /**
     * ---------------------------------------------------------------
     * QRIS PAYMENT STATUS
     * ---------------------------------------------------------------
     *
     * Endpoint ini dipanggil otomatis oleh JavaScript
     * pada halaman payment.qris.
     *
     * Parameter yang diterima adalah encryptedSubscription,
     * BUKAN orderId.
     */
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
}
