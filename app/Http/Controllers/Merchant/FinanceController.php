<?php

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Models\MerchantWallet;
use App\Models\WalletTransaction;
use App\Models\MerchantBankAccount;
use App\Models\Withdrawal;
use App\Models\WebsiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;



class FinanceController extends Controller
{


    public function index(Request $request)
    {


        $merchantId =
            $request->user()
            ->merchant_id;

        $websiteSetting = WebsiteSetting::first();


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

        if ($request->filled('date')) {

            $query->whereDate(
                'created_at',
                $request->date
            );
        }




        if ($request->filled('month')) {

            $query->whereMonth(
                'created_at',
                $request->month
            );
        }




        if ($request->filled('year')) {

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
            ->paginate(
                5,
                ['*'],
                'transaction_page'
            )
            ->withQueryString();

        $withdrawalQuery =
            Withdrawal::where(
                'merchant_id',
                $merchantId
            );



        if ($request->filled('date')) {

            $withdrawalQuery->whereDate(
                'created_at',
                $request->date
            );
        }



        if ($request->filled('month')) {

            $withdrawalQuery->whereMonth(
                'created_at',
                $request->month
            );
        }



        if ($request->filled('year')) {

            $withdrawalQuery->whereYear(
                'created_at',
                $request->year
            );
        }



        $withdrawalQuery->orderBy(
            'created_at',
            $sort
        );



        $withdrawals =
            $withdrawalQuery
            ->paginate(
                5,
                ['*'],
                'withdraw_page'
            )
            ->withQueryString();








        return view(

            'merchant.finance.index',

            compact(

                'wallet',

                'transactions',

                'totalIncome',

                'totalWithdraw',

                'balance',

                'bankAccount',

                'websiteSetting',

                'withdrawals'

            )

        );
    }


    public function withdrawalsRealtime(Request $request)
    {
        $merchantId =
            $request->user()
            ->merchant_id;


        /*
        |--------------------------------------------------------------------------
        | WITHDRAWAL
        |--------------------------------------------------------------------------
        */

        $withdrawals =
            Withdrawal::where(
                'merchant_id',
                $merchantId
            )
            ->latest()
            ->take(10)
            ->get([
                'id',
                'merchant_id',
                'amount',
                'status',
                'note',
                'payout_status',
                'created_at',
                'updated_at',
            ]);


        /*
        |--------------------------------------------------------------------------
        | WALLET
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
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'data' =>
            $withdrawals,

            'summary' => [

                'total_income' =>
                $totalIncome,

                'balance' =>
                $wallet->balance,

                'total_withdraw' =>
                $totalWithdraw,

            ],

        ]);
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
}
