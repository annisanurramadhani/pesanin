<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Throwable;

class MidtransNotificationController extends Controller
{
    /**
     * Menerima notification dari Midtrans.
     */
    public function handle(Request $request)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Konfigurasi Midtrans
            |--------------------------------------------------------------------------
            */

            Config::$serverKey = config(
                'services.midtrans.server_key'
            );

            Config::$isProduction = config(
                'services.midtrans.is_production',
                false
            );

            Config::$isSanitized = true;
            Config::$is3ds = true;


            /*
            |--------------------------------------------------------------------------
            | Ambil Notification Midtrans
            |--------------------------------------------------------------------------
            |
            | Class Notification akan membaca data POST
            | yang dikirim Midtrans.
            |
            */

            $notification = new Notification();


            /*
            |--------------------------------------------------------------------------
            | Data Transaksi
            |--------------------------------------------------------------------------
            */

            $orderId = $notification->order_id;

            $transactionStatus =
                $notification->transaction_status;

            $fraudStatus =
                $notification->fraud_status ?? null;

            $grossAmount =
                $notification->gross_amount;

            $transactionId =
                $notification->transaction_id ?? null;


            /*
            |--------------------------------------------------------------------------
            | Log Notification
            |--------------------------------------------------------------------------
            */

            Log::info(
                'MIDTRANS NOTIFICATION MASUK',
                [

                    'order_id' =>
                        $orderId,

                    'transaction_status' =>
                        $transactionStatus,

                    'fraud_status' =>
                        $fraudStatus,

                    'gross_amount' =>
                        $grossAmount,

                    'transaction_id' =>
                        $transactionId,

                ]
            );


            /*
            |--------------------------------------------------------------------------
            | Validasi Order ID
            |--------------------------------------------------------------------------
            |
            | Format:
            |
            | SUB-10-20260818132910-FYK38N
            |
            */

            if (
                !preg_match(
                    '/^SUB-(\d+)-/',
                    $orderId,
                    $matches
                )
            ) {

                Log::warning(
                    'Format Order ID Midtrans tidak valid.',
                    [
                        'order_id' =>
                            $orderId,
                    ]
                );

                return response()->json([
                    'message' =>
                        'Invalid order ID',
                ], 400);
            }


            /*
            |--------------------------------------------------------------------------
            | Ambil Subscription ID
            |--------------------------------------------------------------------------
            */

            $subscriptionId =
                (int) $matches[1];


            /*
            |--------------------------------------------------------------------------
            | Cari Subscription
            |--------------------------------------------------------------------------
            */

            $subscription =
                Subscription::with([
                    'packageDuration',
                ])
                    ->where(
                        'id',
                        $subscriptionId
                    )
                    ->first();


            if (!$subscription) {

                Log::warning(
                    'Subscription tidak ditemukan.',
                    [

                        'subscription_id' =>
                            $subscriptionId,

                        'order_id' =>
                            $orderId,

                    ]
                );

                return response()->json([
                    'message' =>
                        'Subscription not found',
                ], 404);
            }


            /*
            |--------------------------------------------------------------------------
            | Jika Sudah Aktif
            |--------------------------------------------------------------------------
            |
            | Notification dari Midtrans bisa dikirim lebih dari sekali.
            | Jadi jangan aktifkan ulang subscription.
            |
            */

            if (
                $subscription->status === 'active'
            ) {

                return response()->json([
                    'message' =>
                        'Subscription already active',
                ], 200);
            }


            /*
            |--------------------------------------------------------------------------
            | Validasi Nominal
            |--------------------------------------------------------------------------
            */

            if (
                (float) $grossAmount !==
                (float) $subscription->price
            ) {

                Log::warning(
                    'Nominal pembayaran tidak sesuai.',
                    [

                        'subscription_id' =>
                            $subscription->id,

                        'expected' =>
                            $subscription->price,

                        'received' =>
                            $grossAmount,

                    ]
                );

                return response()->json([
                    'message' =>
                        'Invalid payment amount',
                ], 400);
            }


            /*
            |--------------------------------------------------------------------------
            | PEMBAYARAN BERHASIL
            |--------------------------------------------------------------------------
            */

            $paymentSuccess =

                $transactionStatus === 'settlement'

                ||

                (
                    $transactionStatus === 'capture'
                    &&
                    $fraudStatus === 'accept'
                );


            if ($paymentSuccess) {

                DB::transaction(
                    function () use (
                        $subscription,
                        $orderId,
                        $transactionId
                    ) {

                        /*
                        |----------------------------------------------------------
                        | Start Date
                        |----------------------------------------------------------
                        */

                        $startDate =
                            now()->startOfDay();


                        /*
                        |----------------------------------------------------------
                        | End Date
                        |----------------------------------------------------------
                        */

                        $endDate =
                            $startDate->copy()->addDays(
                                $subscription
                                    ->packageDuration
                                    ->duration_days
                            );


                        /*
                        |----------------------------------------------------------
                        | Update Subscription
                        |----------------------------------------------------------
                        */

                        $subscription->update([

                            'invoice_number' =>
                                $orderId,

                            'start_date' =>
                                $startDate->toDateString(),

                            'end_date' =>
                                $endDate->toDateString(),

                            'paid_at' =>
                                now(),

                            'status' =>
                                'active',

                        ]);


                        /*
                        |----------------------------------------------------------
                        | Log Berhasil
                        |----------------------------------------------------------
                        */

                        Log::info(
                            'SUBSCRIPTION BERHASIL DIAKTIFKAN',
                            [

                                'subscription_id' =>
                                    $subscription->id,

                                'order_id' =>
                                    $orderId,

                                'transaction_id' =>
                                    $transactionId,

                                'start_date' =>
                                    $startDate->toDateString(),

                                'end_date' =>
                                    $endDate->toDateString(),

                            ]
                        );
                    }
                );


                return response()->json([
                    'message' =>
                        'Payment processed successfully',
                ], 200);
            }


            /*
            |--------------------------------------------------------------------------
            | PAYMENT PENDING
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'pending'
            ) {

                Log::info(
                    'PAYMENT MASIH PENDING',
                    [
                        'subscription_id' =>
                            $subscription->id,

                        'order_id' =>
                            $orderId,
                    ]
                );

                return response()->json([
                    'message' =>
                        'Payment pending',
                ], 200);
            }


            /*
            |--------------------------------------------------------------------------
            | PAYMENT GAGAL / EXPIRED
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $transactionStatus,
                    [
                        'deny',
                        'cancel',
                        'expire',
                    ]
                )
            ) {

                Log::info(
                    'PAYMENT GAGAL / EXPIRED',
                    [

                        'subscription_id' =>
                            $subscription->id,

                        'order_id' =>
                            $orderId,

                        'transaction_status' =>
                            $transactionStatus,

                    ]
                );

                /*
                | Untuk sekarang subscription
                | tetap pending.
                |
                | Kalau nanti ingin status "failed",
                | kita bisa tambahkan status tersebut.
                */

                return response()->json([
                    'message' =>
                        'Payment not successful',
                ], 200);
            }


            /*
            |--------------------------------------------------------------------------
            | Status Lain
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'message' =>
                    'Notification received',
            ], 200);


        } catch (Throwable $e) {

            Log::error(
                'MIDTRANS NOTIFICATION ERROR',
                [

                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),

                    'trace' =>
                        $e->getTraceAsString(),

                ]
            );

            return response()->json([
                'message' =>
                    'Internal server error',
            ], 500);
        }
    }
}
