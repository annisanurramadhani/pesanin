<?php

namespace App\Services;

use App\Models\Order;
use App\Models\MerchantWallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;


class WalletService
{


    /**
     * Tambah saldo merchant
     * ketika pembayaran berhasil
     */
    public function addOrderPayment(
        Order $order
    )
    {


        /*
        |--------------------------------------------------------------------------
        | Cegah double credit
        |--------------------------------------------------------------------------
        */

        $exists =
            WalletTransaction::where(
                'reference_type',
                Order::class
            )
            ->where(
                'reference_id',
                $order->id
            )
            ->where(
                'type',
                'credit'
            )
            ->exists();



        if ($exists) {

            return;

        }



        DB::transaction(function () use ($order) {


            $wallet =
                MerchantWallet::firstOrCreate(

                    [
                        'merchant_id'
                        =>
                        $order->merchant_id
                    ],

                    [
                        'balance'
                        =>
                        0
                    ]

                );



            $amount =
                $order->total;



            /*
            |--------------------------------------------------------------------------
            | Tambah saldo
            |--------------------------------------------------------------------------
            */

            $wallet->increment(
                'balance',
                $amount
            );



            /*
            |--------------------------------------------------------------------------
            | Catat transaksi
            |--------------------------------------------------------------------------
            */

            WalletTransaction::create([

            'merchant_id'
                =>
                $order->merchant_id,


            'type'
                =>
                'credit',


            'status'
                =>
                'success',


            'amount'
                =>
                $amount,


            'reference_type'
                =>
                Order::class,


            'reference_id'
                =>
                $order->id,


            'description'
                =>
                'Pembayaran Order #' .
                $order->order_number,

        ]);

        });

    }


}
