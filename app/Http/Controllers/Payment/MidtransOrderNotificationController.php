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
            | MIDTRANS NOTIFICATION
            |--------------------------------------------------------------------------
            */

            $notification = new Notification();


            /*
            |--------------------------------------------------------------------------
            | DATA NOTIFICATION
            |--------------------------------------------------------------------------
            */

            $midtransOrderId = $notification->order_id;

            $transactionStatus = $notification->transaction_status;

            $fraudStatus = $notification->fraud_status ?? null;

            $grossAmount = $notification->gross_amount;

            $transactionId = $notification->transaction_id ?? null;


            /*
            |--------------------------------------------------------------------------
            | LOG NOTIFICATION
            |--------------------------------------------------------------------------
            */

            Log::info(
                'MIDTRANS ORDER NOTIFICATION',
                [
                    'order_id' => $midtransOrderId,

                    'transaction_status' => $transactionStatus,

                    'fraud_status' => $fraudStatus,

                    'gross_amount' => $grossAmount,

                    'transaction_id' => $transactionId,
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | VALIDATE MIDTRANS ORDER ID
            |--------------------------------------------------------------------------
            |
            | Format:
            |
            | ORD-{database_id}-{timestamp}-{random}
            |
            | Contoh:
            |
            | ORD-15-20260824101010-ABC123
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
                        'order_id' => $midtransOrderId,
                    ]
                );

                return response()->json(
                    [
                        'message' => 'Invalid order ID',
                    ],
                    400
                );
            }


            /*
            |--------------------------------------------------------------------------
            | DATABASE ORDER ID
            |--------------------------------------------------------------------------
            */

            $orderId = (int) $matches[1];


            /*
            |--------------------------------------------------------------------------
            | FIND ORDER
            |--------------------------------------------------------------------------
            */

            $order = Order::find($orderId);

            if (!$order) {

                Log::warning(
                    'Order tidak ditemukan.',
                    [
                        'order_id' => $orderId,
                        'midtrans_order_id' => $midtransOrderId,
                    ]
                );

                return response()->json(
                    [
                        'message' => 'Order not found',
                    ],
                    404
                );
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATE GROSS AMOUNT
            |--------------------------------------------------------------------------
            */

            if (
                (float) $grossAmount !==
                (float) $order->total
            ) {

                Log::warning(
                    'Nominal pembayaran order tidak sesuai.',
                    [
                        'order_id' => $order->id,

                        'expected' => $order->total,

                        'received' => $grossAmount,

                        'midtrans_order_id' => $midtransOrderId,
                    ]
                );

                return response()->json(
                    [
                        'message' => 'Invalid payment amount',
                    ],
                    400
                );
            }


            /*
            |--------------------------------------------------------------------------
            | SETTLEMENT
            |--------------------------------------------------------------------------
            |
            | settlement berarti pembayaran berhasil.
            |
            | payment_status:
            | pending -> paid
            |
            | status order TIDAK diubah ke processing.
            | Status order akan diproses oleh merchant/dapur.
            |
            */

            if ($transactionStatus === 'settlement') {

                DB::transaction(
                    function () use (
                        $order,
                        $midtransOrderId,
                        $transactionId
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | CEGAH DUPLICATE NOTIFICATION
                        |--------------------------------------------------------------------------
                        */

                        if ($order->payment_status === 'paid') {

                            Log::info(
                                'PEMBAYARAN ORDER SUDAH DIPROSES SEBELUMNYA.',
                                [
                                    'order_id' =>
                                        $order->id,

                                    'midtrans_order_id' =>
                                        $midtransOrderId,
                                ]
                            );

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE PAYMENT
                        |--------------------------------------------------------------------------
                        */

                        $order->update([
                            'payment_status' =>
                                'paid',

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
                            'PEMBAYARAN ORDER BERHASIL.',
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
            | Beberapa metode pembayaran dapat menghasilkan
            | status capture.
            |
            */

            if ($transactionStatus === 'capture') {

                /*
                |--------------------------------------------------------------------------
                | VALIDATE FRAUD STATUS
                |--------------------------------------------------------------------------
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

                    /*
                    |--------------------------------------------------------------------------
                    | CEGAH DUPLICATE
                    |--------------------------------------------------------------------------
                    */

                    if ($order->payment_status === 'paid') {

                        Log::info(
                            'PEMBAYARAN ORDER CAPTURE SUDAH DIPROSES.',
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
                                    'Payment already processed',
                            ],
                            200
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE PAYMENT
                    |--------------------------------------------------------------------------
                    */

                    $order->update([
                        'payment_status' =>
                            'paid',

                        'payment_provider' =>
                            'midtrans:' .
                            $midtransOrderId,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | LOG
                    |--------------------------------------------------------------------------
                    */

                    Log::info(
                        'PEMBAYARAN ORDER CAPTURE BERHASIL.',
                        [
                            'order_id' =>
                                $order->id,

                            'transaction_id' =>
                                $transactionId,

                            'midtrans_order_id' =>
                                $midtransOrderId,
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
            |
            | Customer belum menyelesaikan pembayaran.
            |
            */

            if ($transactionStatus === 'pending') {

                Log::info(
                    'PEMBAYARAN ORDER MASIH PENDING.',
                    [
                        'order_id' =>
                            $order->id,

                        'midtrans_order_id' =>
                            $midtransOrderId,

                        'transaction_status' =>
                            $transactionStatus,
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
            | DENY
            |--------------------------------------------------------------------------
            |
            | Pembayaran ditolak oleh Midtrans.
            |
            */

            if ($transactionStatus === 'deny') {

                $order->update([
                    'payment_status' =>
                        'failed',

                    'payment_provider' =>
                        'midtrans:' .
                        $midtransOrderId,
                ]);


                Log::info(
                    'PEMBAYARAN ORDER DITOLAK.',
                    [
                        'order_id' =>
                            $order->id,

                        'midtrans_order_id' =>
                            $midtransOrderId,

                        'transaction_id' =>
                            $transactionId,
                    ]
                );


                return response()->json(
                    [
                        'message' =>
                            'Payment denied',
                    ],
                    200
                );
            }


            /*
            |--------------------------------------------------------------------------
            | EXPIRED
            |--------------------------------------------------------------------------
            |
            | QRIS / transaksi sudah melewati batas waktu pembayaran.
            |
            */

            if ($transactionStatus === 'expire') {

                $order->update([
                    'payment_status' =>
                        'expired',

                    'payment_provider' =>
                        'midtrans:' .
                        $midtransOrderId,
                ]);


                Log::info(
                    'PEMBAYARAN ORDER EXPIRED.',
                    [
                        'order_id' =>
                            $order->id,

                        'midtrans_order_id' =>
                            $midtransOrderId,

                        'transaction_id' =>
                            $transactionId,
                    ]
                );


                return response()->json(
                    [
                        'message' =>
                            'Payment expired',
                    ],
                    200
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CANCEL
            |--------------------------------------------------------------------------
            |
            | Transaksi dibatalkan.
            |
            */

            if ($transactionStatus === 'cancel') {

                $order->update([
                    'payment_status' =>
                        'failed',

                    'status' =>
                        'cancelled',

                    'payment_provider' =>
                        'midtrans:' .
                        $midtransOrderId,
                ]);


                Log::info(
                    'PEMBAYARAN ORDER DIBATALKAN.',
                    [
                        'order_id' =>
                            $order->id,

                        'midtrans_order_id' =>
                            $midtransOrderId,

                        'transaction_id' =>
                            $transactionId,
                    ]
                );


                return response()->json(
                    [
                        'message' =>
                            'Payment cancelled',
                    ],
                    200
                );
            }


            /*
            |--------------------------------------------------------------------------
            | STATUS LAIN
            |--------------------------------------------------------------------------
            */

            Log::info(
                'MIDTRANS ORDER STATUS LAIN.',
                [
                    'order_id' =>
                        $order->id,

                    'midtrans_order_id' =>
                        $midtransOrderId,

                    'transaction_status' =>
                        $transactionStatus,
                ]
            );


            return response()->json(
                [
                    'message' =>
                        'Notification received',
                ],
                200
            );

        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | ERROR HANDLING
            |--------------------------------------------------------------------------
            */

            Log::error(
                'MIDTRANS ORDER NOTIFICATION ERROR',
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