<?php

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Models\MerchantWallet;
use App\Models\WalletTransaction;
use App\Models\MerchantBankAccount;
use App\Models\Withdrawal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;



class FinanceController extends Controller
{


    public function index(Request $request)
    {
        

        $merchantId =
            $request->user()
            ->merchant_id;



        /*
        |--------------------------------------------------------------------------
        | WALLET MERCHANT
        |--------------------------------------------------------------------------
        */


        $wallet =
            MerchantWallet::firstOrCreate(

                [
                    'merchant_id'
                    =>
                    $merchantId
                ],

                [
                    'balance'
                    =>
                    0
                ]

            );


            


        /*
        |--------------------------------------------------------------------------
        | REKENING PENARIKAN
        |--------------------------------------------------------------------------
        |
        | Rekening hanya satu
        | Merchant tidak bisa edit
        |
        */


        $bankAccount =
            MerchantBankAccount::where(
                'merchant_id',
                $merchantId
            )
            ->where(
                'status',
                'active'
            )
            ->first();







        /*
        |--------------------------------------------------------------------------
        | TOTAL PEMASUKAN
        |--------------------------------------------------------------------------
        */


        $totalIncome =
            WalletTransaction::where(
                'merchant_id',
                $merchantId
            )
            ->where(
                'type',
                'credit'
            )
            ->sum('amount');








        /*
        |--------------------------------------------------------------------------
        | TOTAL PENARIKAN
        |--------------------------------------------------------------------------
        */


        $totalWithdraw =
            WalletTransaction::where(
                'merchant_id',
                $merchantId
            )
            ->where(
                'type',
                'debit'
            )
            ->sum('amount');








        /*
        |--------------------------------------------------------------------------
        | SALDO TERSEDIA
        |--------------------------------------------------------------------------
        */


        $balance =
            $wallet->balance;








        /*
        |--------------------------------------------------------------------------
        | FILTER TRANSAKSI
        |--------------------------------------------------------------------------
        */


        $query =
            WalletTransaction::where(
                'merchant_id',
                $merchantId
            );





        if($request->filled('date'))
        {

            $query->whereDate(
                'created_at',
                $request->date
            );

        }




        if($request->filled('month'))
        {

            $query->whereMonth(
                'created_at',
                $request->month
            );

        }




        if($request->filled('year'))
        {

            $query->whereYear(
                'created_at',
                $request->year
            );

        }







        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        */


        $sort =
            $request->get(
                'sort',
                'desc'
            );



        $query->orderBy(
            'created_at',
            $sort
        );





        $transactions =
            $query
            ->paginate(10)
            ->withQueryString();








        return view(

            'merchant.finance.index',

            compact(

                'wallet',

                'transactions',

                'totalIncome',

                'totalWithdraw',

                'balance',

                'bankAccount'

            )

        );

    }









    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */


    public function pdf(Request $request)
    {


        $merchantId =
            $request->user()
            ->merchant_id;





        $wallet =
            MerchantWallet::firstOrCreate(

                [
                    'merchant_id'
                    =>
                    $merchantId
                ],

                [
                    'balance'
                    =>
                    0
                ]

            );






        $transactions =
            WalletTransaction::where(
                'merchant_id',
                $merchantId
            )
            ->latest()
            ->get();






        $pdf =
            Pdf::loadView(

                'merchant.finance.pdf',

                compact(

                    'transactions',

                    'wallet'

                )

            );






        return $pdf->download(

            'laporan-keuangan.pdf'

        );


    }









    /*
    |--------------------------------------------------------------------------
    | AJUKAN PENARIKAN SALDO
    |--------------------------------------------------------------------------
    */


    public function withdraw(Request $request)
    {


        $request->validate([


            'amount'
            =>
            [

                'required',

                'numeric',

                'min:10000'

            ]


        ]);







        $merchantId =
            $request->user()
            ->merchant_id;








        /*
        |--------------------------------------------------------------------------
        | CEK REKENING
        |--------------------------------------------------------------------------
        */


        $bankAccount =
            MerchantBankAccount::where(
                'merchant_id',
                $merchantId
            )
            ->where(
                'status',
                'active'
            )
            ->first();






        if(!$bankAccount)
        {

            return back()->with(

                'error',

                'Silahkan tambahkan rekening penarikan terlebih dahulu'

            );

        }









        /*
        |--------------------------------------------------------------------------
        | CEK WALLET
        |--------------------------------------------------------------------------
        */


        $wallet =
            MerchantWallet::where(
                'merchant_id',
                $merchantId
            )
            ->first();






        if(!$wallet)
        {

            return back()->with(

                'error',

                'Wallet tidak ditemukan'

            );

        }







        if($wallet->balance < $request->amount)
        {


            return back()->with(

                'error',

                'Saldo tidak mencukupi'

            );


        }








        /*
        |--------------------------------------------------------------------------
        | SEMENTARA
        |--------------------------------------------------------------------------
        |
        | Nanti bagian ini pindah ke withdrawals table
        |
        | Tidak langsung mengurangi saldo
        |
        */


        return back()->with(

            'success',

            'Permintaan penarikan berhasil dikirim ke admin'

        );


    }


}
