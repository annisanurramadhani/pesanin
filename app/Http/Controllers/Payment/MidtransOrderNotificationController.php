<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Throwable;

class MidtransOrderNotificationController extends Controller
{
    /**
     * Menerima notification pembayaran order dari Midtrans.
     */
    public function handle(Request $request)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | MIDTRANS CONFIG
            |--------------------------------------------------------------------------
            */

            Config::$serverKey =
                config('services.midtrans.server_key');

            Config::$isProduction =
                config(
                    'services.midtrans.is_production',
                    false
                );

            Config::$isSanitized = true;

            Config::$is3ds = true;


            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION
            |--------------------------------------------------------------------------
            */

            $notification =
                new Notification();


            /*
            |--------------------------------------------------------------------------
            | DATA
            |--------------------------------------------------------------------------
            */

            $midtransOrderId =
                $notification->order_id;

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
            | LOG
            |--------------------------------------------------------------------------
            */

            Log::info(
                'MIDTRANS ORDER NOTIFICATION',
                [

                    'order_id' =>
                        $midtransOrderId,

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
            | VALIDATE ORDER ID
            |--------------------------------------------------------------------------
            |
            | Format:
            |
            | ORD-{database_id}-{timestamp}-{random}
            |
            */

            if (
                !preg_match(
                    '/^ORD-(\d+)-/',
                    $midtransOrderId,
                    $matches
                )
            ) {

                Log::warning(
                    'Format Order ID Midtrans tidak valid.',
                    [
                        'order_id' =>
                            $midtransOrderId,
                    ]
                );

                return response()->json(
                    [
                        'message' =>
                            'Invalid order ID',
                    ],
                    400
                );
            }


            /*
            |--------------------------------------------------------------------------
            | DATABASE ORDER ID
            |--------------------------------------------------------------------------
            */

            $orderId =
                (int) $matches[1];


            /*
            |--------------------------------------------------------------------------
            | FIND ORDER
            |--------------------------------------------------------------------------
            */

            $order =
                Order::find($orderId);


            if (!$order) {

                Log::warning(
                    'Order tidak ditemukan.',
                    [
                        'order_id' =>
                            $orderId,
                    ]
                );

                return response()->json(
                    [
                        'message' =>
                            'Order not found',
                    ],
                    404
                );
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATE AMOUNT
            |--------------------------------------------------------------------------
            */

            if (
                (float) $grossAmount !==
                (float) $order->total
            ) {

                Log::warning(
                    'Nominal pembayaran order tidak sesuai.',
                    [

                        'order_id' =>
                            $order->id,

                        'expected' =>
                            $order->total,

                        'received' =>
                            $grossAmount,
                    ]
                );

                return response()->json(
                    [
                        'message' =>
                            'Invalid payment amount',
                    ],
                    400
                );
            }


            /*
            |--------------------------------------------------------------------------
            | SETTLEMENT
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'settlement'
            ) {

                DB::transaction(
                    function () use (
                        $order,
                        $midtransOrderId,
                        $transactionId
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | Jangan proses ulang
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $order->status !== 'pending'
                        ) {
                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE ORDER
                        |--------------------------------------------------------------------------
                        */

                        $order->update([

                            'status' =>
                                'processing',

                            'payment_provider' =>
                                'midtrans:' .
                                $midtransOrderId,
                        ]);


                        /*
                        |--------------------------------------------------------------------------
                        | LOG SUCCESS
                        |--------------------------------------------------------------------------
                        */

                        Log::info(
                            'PEMBAYARAN ORDER BERHASIL',
                            [

                                'order_id' =>
                                    $order->id,

                                'order_number' =>
                                    $order->order_number,

                                'midtrans_order_id' =>
                                    $midtransOrderId,

                                'transaction_id' =>
                                    $transactionId,
                            ]
                        );
                    }
                );


                return response()->json(
                    [
                        'message' =>
                            'Order payment processed successfully',
                    ],
                    200
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CAPTURE
            |--------------------------------------------------------------------------
            |
            | Beberapa metode pembayaran bisa menghasilkan
            | status capture.
            |
            */

            if (
                $transactionStatus === 'capture'
            ) {

                /*
                |----------------------------------------------------------------------
                | Untuk sandbox/test, anggap capture berhasil
                |----------------------------------------------------------------------
                */

                if (
                    in_array(
                        $fraudStatus,
                        [
                            null,
                            'accept',
                        ],
                        true
                    )
                ) {

                    $order->update([

                        'status' =>
                            'processing',

                        'payment_provider' =>
                            'midtrans:' .
                            $midtransOrderId,
                    ]);


                    Log::info(
                        'PEMBAYARAN ORDER CAPTURE',
                        [
                            'order_id' =>
                                $order->id,

                            'transaction_id' =>
                                $transactionId,
                        ]
                    );
                }


                return response()->json(
                    [
                        'message' =>
                            'Payment captured',
                    ],
                    200
                );
            }


            /*
            |--------------------------------------------------------------------------
            | PENDING
            |--------------------------------------------------------------------------
            */

            if (
                $transactionStatus === 'pending'
            ) {

                Log::info(
                    'PEMBAYARAN ORDER MASIH PENDING',
                    [

                        'order_id' =>
                            $order->id,

                        'midtrans_order_id' =>
                            $midtransOrderId,
                    ]
                );

                return response()->json(
                    [
                        'message' =>
                            'Payment pending',
                    ],
                    200
                );
            }


            /*
            |--------------------------------------------------------------------------
            | FAILED / EXPIRED
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $transactionStatus,
                    [
                        'deny',
                        'cancel',
                        'expire',
                    ],
                    true
                )
            ) {

                $order->update([
                    'status' => 'cancelled',
                ]);


                Log::info(
                    'PEMBAYARAN ORDER GAGAL / EXPIRED',
                    [

                        'order_id' =>
                            $order->id,

                        'transaction_status' =>
                            $transactionStatus,
                    ]
                );


                return response()->json(
                    [
                        'message' =>
                            'Payment not successful',
                    ],
                    200
                );
            }


            /*
            |--------------------------------------------------------------------------
            | OTHER STATUS
            |--------------------------------------------------------------------------
            */

            return response()->json(
                [
                    'message' =>
                        'Notification received',
                ],
                200
            );

        } catch (Throwable $e) {

            Log::error(
                'MIDTRANS ORDER NOTIFICATION ERROR',
                [

                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),
                ]
            );


            return response()->json(
                [
                    'message' =>
                        'Internal server error',
                ],
                500
            );
        }
    }
}
