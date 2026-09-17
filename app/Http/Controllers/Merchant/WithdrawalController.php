<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Withdrawal;
use App\Models\MerchantWallet;

class WithdrawalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:10000'
            ]
        ]);

        $user = Auth::user();

        $merchant = $user->merchant;

        if(!$merchant){

            return back()->with(
                'error',
                'Merchant tidak ditemukan'
            );

        }


        $wallet = MerchantWallet::where(
            'merchant_id',
            $merchant->id
        )
        ->first();


        if(!$wallet){

            return back()->with(
                'error',
                'Wallet merchant tidak ditemukan'
            );

        }


        if($request->amount > $wallet->balance){

            return back()->with(
                'error',
                'Saldo tidak mencukupi'
            );

        }


        $bankAccount = $merchant->bankAccount;


        if(!$bankAccount){

            return back()->with(
                'error',
                'Rekening bank belum tersedia'
            );

        }


        $pending = Withdrawal::where(
            'merchant_id',
            $merchant->id
        )
        ->where(
            'status',
            'pending'
        )
        ->exists();


        if($pending){

            return back()->with(
                'error',
                'Masih ada pengajuan penarikan yang sedang diproses'
            );

        }


        Withdrawal::create([

            'merchant_id' => $merchant->id,

            'merchant_bank_account_id' => $bankAccount->id,

            'amount' => $request->amount,

            'status' => 'pending',

        ]);


        return back()->with(
            'success',
            'Pengajuan penarikan berhasil dikirim ke admin'
        );
    }
}