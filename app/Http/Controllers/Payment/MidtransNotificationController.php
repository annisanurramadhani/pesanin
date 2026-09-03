<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderReceiptMail;
use App\Models\Order;
use App\Models\Subscription;
use App\Notifications\SubscriptionInvoiceNotification;
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
            */

            $notification = new Notification();


            /*
            |--------------------------------------------------------------------------
            | Data Transaksi
            |--------------------------------------------------------------------------
            */

            $orderId =
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
            |--------------------------------------------------------------------------
            | ORDER CUSTOMER / QRIS
            |--------------------------------------------------------------------------
            |--------------------------------------------------------------------------
            |
            | Format Order ID:
            |
            | ORD-{order_id}-{timestamp}-{random}
            |
            */

            if (
                preg_match(
                    '/^ORD-(\d+)-/',
                    $orderId,
                    $matches
                )
            ) {

                $orderDatabaseId =
                    (int) $matches[1];


                /*
                |--------------------------------------------------------------------------
                | Cari Order
                |--------------------------------------------------------------------------
                */

                $order = Order::find(
                    $orderDatabaseId
                );


                if (!$order) {

                    Log::warning(
                        'Order Customer tidak ditemukan.',
                        [

                            'order_id' =>
                            $orderId,

                            'order_database_id' =>
                            $orderDatabaseId,

                        ]
                    );

                    return response()->json([
                        'message' =>
                        'Order not found',
                    ], 404);
                }


                /*
                |--------------------------------------------------------------------------
                | Pastikan Payment Provider Sesuai
                |--------------------------------------------------------------------------
                |
                | Mencegah notification Midtrans untuk transaksi
                | lain memproses order yang salah.
                |
                */

                if (
                    $order->payment_provider !==
                    'midtrans:' . $orderId
                ) {

                    Log::warning(
                        'Payment provider order tidak sesuai.',
                        [

                            'order_id' =>
                            $order->id,

                            'expected' =>
                            'midtrans:' . $orderId,

                            'actual' =>
                            $order->payment_provider,

                        ]
                    );

                    return response()->json([
                        'message' =>
                        'Invalid payment provider',
                    ], 400);
                }


                /*
                |--------------------------------------------------------------------------
                | Validasi Nominal
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

                            'midtrans_order_id' =>
                            $orderId,

                            'expected' =>
                            $order->total,

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
                | PAYMENT BERHASIL
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

                    /*
                    |--------------------------------------------------------------------------
                    | PAYMENT CUSTOMER SUDAH PERNAH DIPROSES
                    |--------------------------------------------------------------------------
                    |
                    | Jangan memproses pembayaran ulang.
                    | Tetapi jika email belum terkirim, tetap coba kirim struk.
                    |
                    */

                    if (
                        $order->payment_status === 'paid'
                    ) {

                        Log::info(
                            'PAYMENT ORDER SUDAH DIPROSES.',
                            [

                                'order_id' =>
                                $order->id,

                                'midtrans_order_id' =>
                                $orderId,

                            ]
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | KIRIM ULANG STRUK JIKA BELUM TERKIRIM
                        |--------------------------------------------------------------------------
                        |
                        | Kondisi:
                        |
                        | customer_email ada
                        | receipt_sent_at masih NULL
                        |
                        */

                        if (
                            !empty($order->customer_email)
                            &&
                            is_null($order->receipt_sent_at)
                        ) {

                            try {

                                /*
                                |--------------------------------------------------------------------------
                                | Load relasi yang digunakan Blade email
                                |--------------------------------------------------------------------------
                                */

                                $order->load([
                                    'merchant',
                                    'qrCode',
                                    'items',
                                ]);


                                /*
                                |--------------------------------------------------------------------------
                                | Kirim Email Struk
                                |--------------------------------------------------------------------------
                                */

                                Mail::to(
                                    $order->customer_email
                                )->send(
                                    new OrderReceiptMail($order)
                                );


                                /*
                                |--------------------------------------------------------------------------
                                | Tandai Struk Sudah Terkirim
                                |--------------------------------------------------------------------------
                                */

                                $order->update([
                                    'receipt_sent_at' => now(),
                                ]);


                                Log::info(
                                    'STRUK CUSTOMER BERHASIL DIKIRIM.',
                                    [

                                        'order_id' =>
                                        $order->id,

                                        'order_number' =>
                                        $order->order_number,

                                        'email' =>
                                        $order->customer_email,

                                    ]
                                );
                            } catch (Throwable $e) {

                                /*
                                |--------------------------------------------------------------------------
                                | Email gagal tidak membatalkan pembayaran
                                |--------------------------------------------------------------------------
                                */

                                Log::error(
                                    'GAGAL MENGIRIM STRUK CUSTOMER.',
                                    [

                                        'order_id' =>
                                        $order->id,

                                        'order_number' =>
                                        $order->order_number,

                                        'email' =>
                                        $order->customer_email,

                                        'message' =>
                                        $e->getMessage(),

                                    ]
                                );
                            }
                        }


                        return response()->json([
                            'message' =>
                            'Order payment already processed',
                        ], 200);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE PAYMENT STATUS
                    |--------------------------------------------------------------------------
                    */

                    DB::transaction(
                        function () use (
                            $order,
                            $transactionId,
                            $transactionStatus
                        ) {

                            $order->update([

                                'payment_status' =>
                                'paid',

                                // Status order sengaja TIDAK diubah.
                                // Nanti dapur yang mengubah status.

                            ]);


                            /*
                            |----------------------------------------------------------
                            | Log Berhasil
                            |----------------------------------------------------------
                            */

                            Log::info(
                                'PEMBAYARAN ORDER BERHASIL.',
                                [

                                    'order_id' =>
                                    $order->id,

                                    'order_number' =>
                                    $order->order_number,

                                    'transaction_id' =>
                                    $transactionId,

                                    'transaction_status' =>
                                    $transactionStatus,

                                ]
                            );
                        }
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | KIRIM STRUK CUSTOMER
                    |--------------------------------------------------------------------------
                    |
                    | Email customer nullable.
                    |
                    | Jika email diisi:
                    |     → kirim struk
                    |
                    | Jika email kosong:
                    |     → tidak kirim apa-apa
                    |
                    | receipt_sent_at digunakan agar email tidak terkirim dua kali.
                    |
                    */

                    if (
                        !empty($order->customer_email)
                        &&
                        is_null($order->receipt_sent_at)
                    ) {

                        try {

                            /*
                            |--------------------------------------------------------------------------
                            | Load relasi untuk Blade email
                            |--------------------------------------------------------------------------
                            */

                            $order->load([
                                'merchant',
                                'qrCode',
                                'items',
                            ]);


                            /*
                            |--------------------------------------------------------------------------
                            | Kirim Email
                            |--------------------------------------------------------------------------
                            */

                            Mail::to(
                                $order->customer_email
                            )->send(
                                new OrderReceiptMail($order)
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | Tandai Sudah Terkirim
                            |--------------------------------------------------------------------------
                            */

                            $order->update([
                                'receipt_sent_at' => now(),
                            ]);


                            Log::info(
                                'STRUK CUSTOMER BERHASIL DIKIRIM.',
                                [

                                    'order_id' =>
                                    $order->id,

                                    'order_number' =>
                                    $order->order_number,

                                    'email' =>
                                    $order->customer_email,

                                ]
                            );
                        } catch (Throwable $e) {

                            /*
                            |--------------------------------------------------------------------------
                            | Email gagal tidak menggagalkan pembayaran
                            |--------------------------------------------------------------------------
                            */

                            Log::error(
                                'GAGAL MENGIRIM STRUK CUSTOMER.',
                                [

                                    'order_id' =>
                                    $order->id,

                                    'order_number' =>
                                    $order->order_number,

                                    'email' =>
                                    $order->customer_email,

                                    'message' =>
                                    $e->getMessage(),

                                ]
                            );
                        }
                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | Email kosong / sudah pernah dikirim
                        |--------------------------------------------------------------------------
                        */

                        Log::info(
                            'STRUK CUSTOMER TIDAK DIKIRIM.',
                            [

                                'order_id' =>
                                $order->id,

                                'order_number' =>
                                $order->order_number,

                                'customer_email' =>
                                $order->customer_email,

                                'receipt_sent_at' =>
                                $order->receipt_sent_at,

                            ]
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | RESPONSE
                    |--------------------------------------------------------------------------
                    */

                    return response()->json([
                        'message' =>
                        'Order payment processed successfully',
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

                    /*
                    |------------------------------------------------------------------
                    | Pastikan tetap pending
                    |------------------------------------------------------------------
                    */

                    if (
                        $order->payment_status !== 'paid'
                    ) {

                        $order->update([
                            'payment_status' =>
                            'pending',
                        ]);
                    }


                    Log::info(
                        'PAYMENT ORDER MASIH PENDING.',
                        [

                            'order_id' =>
                            $order->id,

                            'order_number' =>
                            $order->order_number,

                            'midtrans_order_id' =>
                            $orderId,

                        ]
                    );


                    return response()->json([
                        'message' =>
                        'Order payment pending',
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

                    /*
                    |------------------------------------------------------------------
                    | Jangan ubah payment yang sudah paid
                    |------------------------------------------------------------------
                    */

                    if (
                        $order->payment_status !== 'paid'
                    ) {

                        $order->update([

                            'payment_status' =>
                            $transactionStatus === 'expire'
                                ? 'expired'
                                : 'failed',

                            'status' =>
                            'cancelled',

                        ]);
                    }


                    Log::info(
                        'PAYMENT ORDER GAGAL / EXPIRED.',
                        [

                            'order_id' =>
                            $order->id,

                            'order_number' =>
                            $order->order_number,

                            'midtrans_order_id' =>
                            $orderId,

                            'transaction_status' =>
                            $transactionStatus,

                        ]
                    );


                    return response()->json([
                        'message' =>
                        'Order payment not successful',
                    ], 200);
                }


                /*
                |--------------------------------------------------------------------------
                | Status Lain
                |--------------------------------------------------------------------------
                */

                return response()->json([
                    'message' =>
                    'Order notification received',
                ], 200);
            }


            /*
            |--------------------------------------------------------------------------
            |--------------------------------------------------------------------------
            | SUBSCRIPTION
            |--------------------------------------------------------------------------
            |--------------------------------------------------------------------------
            |
            | BAGIAN INI TETAP MENGGUNAKAN FLOW SUBSCRIPTION YANG SUDAH ADA.
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
                    'packageDuration.package',
                    'merchant.users',
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


                /*
                |--------------------------------------------------------------------------
                | Cek Apakah Invoice Sudah Pernah Diproses
                |--------------------------------------------------------------------------
                */

                $invoiceAlreadyProcessed =
                    !is_null(
                        $subscription->paid_at
                    );


                /*
                |--------------------------------------------------------------------------
                | Update Subscription
                |--------------------------------------------------------------------------
                */

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
                                max(
                                    0,
                                    $subscription
                                        ->packageDuration
                                        ->duration_days - 1
                                )
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


                /*
                |--------------------------------------------------------------------------
                | Refresh Subscription
                |--------------------------------------------------------------------------
                */

                $subscription->refresh();


                /*
                |--------------------------------------------------------------------------
                | Kirim Invoice ke Email
                |--------------------------------------------------------------------------
                |
                | Hanya dikirim sekali.
                |
                */

                if (
                    !$invoiceAlreadyProcessed
                ) {

                    $user =
                        $subscription
                        ->merchant
                        ->users()
                        ->where(
                            'status',
                            'active'
                        )
                        ->orderBy('id')
                        ->first();


                    if ($user) {

                        try {

                            $user->notify(
                                new SubscriptionInvoiceNotification(
                                    $subscription
                                )
                            );


                            Log::info(
                                'INVOICE SUBSCRIPTION BERHASIL DIKIRIM.',
                                [

                                    'subscription_id' =>
                                    $subscription->id,

                                    'invoice_number' =>
                                    $subscription->invoice_number,

                                    'email' =>
                                    $user->email,

                                ]
                            );
                        } catch (Throwable $e) {

                            /*
                            |--------------------------------------------------------------------------
                            | Email gagal tidak membatalkan pembayaran
                            |--------------------------------------------------------------------------
                            */

                            Log::error(
                                'GAGAL MENGIRIM INVOICE SUBSCRIPTION.',
                                [

                                    'subscription_id' =>
                                    $subscription->id,

                                    'invoice_number' =>
                                    $subscription->invoice_number,

                                    'email' =>
                                    $user->email,

                                    'message' =>
                                    $e->getMessage(),

                                ]
                            );
                        }
                    } else {

                        Log::warning(
                            'User aktif untuk invoice tidak ditemukan.',
                            [

                                'subscription_id' =>
                                $subscription->id,

                                'merchant_id' =>
                                $subscription->merchant_id,

                            ]
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Response
                |--------------------------------------------------------------------------
                */

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
