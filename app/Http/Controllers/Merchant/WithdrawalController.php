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
    public function index(Request $request)
    {
        return redirect()->route('merchant.finance.index');
    }

    public function store(Request $request)
    {
        $merchant = $request->user()->merchant;

        if (! $merchant) {
            return back()->with('error', 'Merchant tidak ditemukan.');
        }

        $identity = DB::table('merchant_kyc_submissions')
            ->where('merchant_id', $merchant->id)
            ->first();

        if (! $identity || $identity->status !== 'approved') {
            return redirect()
                ->route('merchant.kyc.index')
                ->with('error', 'Verifikasi identitas harus disetujui sebelum melakukan penarikan saldo.');
        }

        $bankAccount = MerchantBankAccount::where('merchant_id', $merchant->id)
            ->where('status', 'active')
            ->where('is_locked', true)
            ->first();

        if (! $bankAccount) {
            return redirect()
                ->route('merchant.kyc.index')
                ->with('error', 'Verifikasi rekening bank harus disetujui sebelum melakukan penarikan saldo.');
        }

        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:10000',
            ],
        ]);

        try {
            DB::transaction(function () use ($merchant, $request) {
                $identity = DB::table('merchant_kyc_submissions')
                    ->where('merchant_id', $merchant->id)
                    ->lockForUpdate()
                    ->first();

                if (! $identity || $identity->status !== 'approved') {
                    throw new \DomainException('Verifikasi identitas belum disetujui.');
                }

                $wallet = MerchantWallet::where('merchant_id', $merchant->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $bankAccount = MerchantBankAccount::where('merchant_id', $merchant->id)
                    ->where('status', 'active')
                    ->where('is_locked', true)
                    ->lockForUpdate()
                    ->first();

                if (! $bankAccount) {
                    throw new \DomainException('Verifikasi rekening bank belum disetujui.');
                }

                $amount = (float) $request->amount;

                if ($amount > $wallet->balance) {
                    throw new \DomainException('Saldo tidak mencukupi.');
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

        return back()->with('success', 'Pengajuan penarikan berhasil dikirim ke admin.');
    }
}
