<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Throwable;

class PaymentController extends Controller
{
    /**
     * Menampilkan halaman pembayaran Midtrans.
     */
    public function show(string $encryptedSubscription)
    {
        /*
        |--------------------------------------------------------------------------
        | Dekripsi ID Subscription
        |--------------------------------------------------------------------------
        */

        $subscriptionId = decryptId($encryptedSubscription);

        if (!$subscriptionId) {
            return redirect()
                ->route('public.subscription.index')
                ->with(
                    'error',
                    'ID subscription tidak valid.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if (!$user) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan User Memiliki Merchant
        |--------------------------------------------------------------------------
        */

        if (!$user->merchant_id) {
            return redirect()
                ->route('merchant.setup')
                ->with(
                    'error',
                    'Data toko belum tersedia.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil Subscription
        |--------------------------------------------------------------------------
        */

        $subscription = Subscription::with([
            'merchant',
            'packageDuration.package',
        ])
            ->where('id', $subscriptionId)
            ->where('merchant_id', $user->merchant_id)
            ->first();


        if (!$subscription) {
            return redirect()
                ->route('public.subscription.index')
                ->with(
                    'error',
                    'Data subscription tidak ditemukan.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Jika Subscription Sudah Aktif
        |--------------------------------------------------------------------------
        */

        if ($subscription->status === 'active') {
            return redirect()
                ->route('dashboard')
                ->with(
                    'info',
                    'Subscription kamu sudah aktif.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Hanya Pending yang Bisa Dibayar
        |--------------------------------------------------------------------------
        */

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
        | Validasi Server Key
        |--------------------------------------------------------------------------
        */

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
        |
        | Format:
        |
        | SUB-{subscription_id}-{timestamp}-{random}
        |
        | Contoh:
        |
        | SUB-10-20260818132910-FYK38N
        |
        */

        $orderId =
            'SUB-' .
            $subscription->id .
            '-' .
            now()->format('YmdHis') .
            '-' .
            strtoupper(
                str()->random(6)
            );


        /*
        |--------------------------------------------------------------------------
        | Parameter Midtrans
        |--------------------------------------------------------------------------
        */

        $params = [

            'transaction_details' => [

                'order_id' => $orderId,

                'gross_amount' => (int) $subscription->price,

            ],


            'item_details' => [

                [
                    'id' => 'SUB-' . $subscription->id,

                    'price' => (int) $subscription->price,

                    'quantity' => 1,

                    'name' =>
                        'Langganan ' .
                        $subscription->packageDuration->package->name .
                        ' - ' .
                        $subscription->packageDuration->name,
                ],

            ],


            'customer_details' => [

                'first_name' => $user->name,

                'email' => $user->email,

            ],

        ];


        /*
        |--------------------------------------------------------------------------
        | Generate Snap Token
        |--------------------------------------------------------------------------
        */

        try {

            $snapToken = Snap::getSnapToken(
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
        | Tampilkan Halaman Payment
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
     * Tidak digunakan lagi untuk pembayaran.
     *
     * Pembayaran dilakukan melalui Midtrans Snap.
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
