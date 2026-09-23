<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\MerchantBankAccount;
use App\Models\MerchantWallet;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        return redirect()->route('merchant.finance.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:10000',
            ],
        ]);

        $merchant = $request->user()->merchant;

        if (! $merchant) {
            return back()->with(
                'error',
                'Merchant tidak ditemukan.'
            );
        }

        try {
            DB::transaction(function () use ($merchant, $request) {
                // This row lock serializes withdrawal requests for one merchant.
                $wallet = MerchantWallet::where('merchant_id', $merchant->id)->lockForUpdate()->firstOrFail();
                $amount = (float) $request->amount;

                if ($amount > $wallet->balance) {
                    throw new \DomainException('Saldo tidak mencukupi.');
                }

                $bankAccount = MerchantBankAccount::where('merchant_id', $merchant->id)
                    ->where('status', 'active')
                    ->lockForUpdate()
                    ->first();

                if (! $bankAccount) {
                    throw new \DomainException('Rekening bank aktif belum tersedia.');
                }

                if (Withdrawal::where('merchant_id', $merchant->id)
                    ->whereIn('status', ['pending', 'processing'])
                    ->exists()) {
                    throw new \DomainException('Masih ada pengajuan penarikan yang sedang diproses.');
                }

                Withdrawal::create([
                    'merchant_id' => $merchant->id,
                    'merchant_bank_account_id' => $bankAccount->id,
                    'amount' => $amount,
                    'status' => 'pending',
                ]);
            });
        } catch (\DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (ModelNotFoundException) {
            return back()->with('error', 'Wallet merchant tidak ditemukan.');
        }

        return back()->with(
            'success',
            'Pengajuan penarikan berhasil dikirim ke admin.'
        );
    }
}
