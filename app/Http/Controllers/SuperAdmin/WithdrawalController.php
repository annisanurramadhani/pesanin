<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\MerchantWallet;
use App\Models\WalletTransaction;
use App\Services\MidtransPayoutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{

    public function index()
    {

        $withdrawals =
            Withdrawal::with([
                'merchant',
                'bankAccount'
            ])
            ->latest()
            ->paginate(10);


        return view(
            'super_admin.withdrawals.index',
            compact('withdrawals')
        );

    }



    public function approve(
        Withdrawal $withdrawal,
        MidtransPayoutService $payoutService
    )
    {


        if($withdrawal->status !== 'pending')
        {

            return back()->with(
                'error',
                'Withdrawal sudah diproses'
            );

        }



        DB::transaction(function() use (
            $withdrawal,
            $payoutService
        ){


            $wallet =
                MerchantWallet::where(
                    'merchant_id',
                    $withdrawal->merchant_id
                )
                ->firstOrFail();



            if(
                $wallet->balance 
                <
                $withdrawal->amount
            ){

                throw new \Exception(
                    'Saldo merchant tidak mencukupi'
                );

            }



            $withdrawal->update([

                'status'=>'processing',

                'approved_by'=>auth()->id(),

                'approved_at'=>now(),

            ]);




            $result =
                $payoutService->process(
                    $withdrawal
                );
            $withdrawal->update([

                'payout_id' =>
                    $result['payout_id'],

                'payout_status' =>
                    $result['status'],

                'payout_response' =>
                    $result['response'],

            ]);




            if(!$result['success'])
            {


                $withdrawal->update([

                    'status'=>'failed'

                ]);


                throw new \Exception(
                    'Payout Midtrans gagal'
                );


            }





            $wallet->decrement(
                'balance',
                $withdrawal->amount
            );





            WalletTransaction::create([

                'merchant_id'=>
                $withdrawal->merchant_id,


                'type'=>
                'debit',


                'status'=>
                'success',


                'amount'=>
                $withdrawal->amount,


                'reference_type'=>
                Withdrawal::class,


                'reference_id'=>
                $withdrawal->id,


                'description'=>
                'Penarikan saldo merchant',

            ]);






            $withdrawal->update([

                'status'=>
                $result['status'],


                'payout_id'=>
                $result['payout_id'],


                'paid_at'=>
                now(),

            ]);



        });





        return back()->with(
            'success',
            'Penarikan berhasil diproses'
        );


    }






    public function reject(
        Request $request,
        Withdrawal $withdrawal
    )
    {


        if($withdrawal->status !== 'pending')
        {

            return back();

        }



        $withdrawal->update([

            'status'=>'rejected',

            'approved_by'=>auth()->id(),

            'approved_at'=>now(),

            'note'=>$request->note,

        ]);



        return back()->with(
            'success',
            'Penarikan ditolak'
        );


    }

}