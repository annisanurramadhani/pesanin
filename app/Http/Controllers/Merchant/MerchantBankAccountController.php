<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\MerchantBankAccount;
use Illuminate\Http\Request;

class MerchantBankAccountController extends Controller
{

    public function create(Request $request)
    {
        $merchantId = $request->user()->merchant_id;

        $exists = MerchantBankAccount::where(
            'merchant_id',
            $merchantId
        )->exists();


        if ($exists) {

            return redirect()
                ->route('merchant.finance.index')
                ->with(
                    'error',
                    'Rekening sudah tersimpan. Hubungi admin jika ingin mengganti.'
                );

        }


        return view(
            'merchant.finance.create'
        );
    }



    public function store(Request $request)
    {
        $request->validate([

            'bank_name' =>
                'required|string|max:100',

            'account_number' =>
                'required|string|max:50',

            'account_name' =>
                'required|string|max:100',

        ]);


        $merchantId = $request->user()->merchant_id;


        $exists = MerchantBankAccount::where(
            'merchant_id',
            $merchantId
        )->exists();



        if ($exists) {

            return back()
                ->with(
                    'error',
                    'Rekening sudah tersimpan.'
                );

        }



        MerchantBankAccount::create([

            'merchant_id' =>
                $merchantId,

            'bank_name' =>
                $request->bank_name,

            'account_number' =>
                $request->account_number,

            'account_name' =>
                $request->account_name,

            'status' =>
                'active',

            'is_locked' =>
                true,

        ]);



        return redirect()
            ->route('merchant.finance.index')
            ->with(
                'success',
                'Rekening berhasil disimpan.'
            );
    }

}