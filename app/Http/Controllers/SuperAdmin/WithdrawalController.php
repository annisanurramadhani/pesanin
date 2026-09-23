<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MerchantWallet;
use App\Models\Withdrawal;
use App\Services\MidtransPayoutService;
use App\Services\WithdrawalSettlementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {

        $withdrawals =
            Withdrawal::with([
                'merchant',
                'bankAccount',
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
        MidtransPayoutService $payoutService,
        WithdrawalSettlementService $settlement
    ) {

        try {
            $withdrawal = DB::transaction(function () use ($withdrawal) {
                $lockedWithdrawal = Withdrawal::whereKey($withdrawal->id)->lockForUpdate()->firstOrFail();

                if ($lockedWithdrawal->status !== 'pending') {
                    throw new \DomainException('Withdrawal sudah diproses.');
                }

                $wallet = MerchantWallet::where('merchant_id', $lockedWithdrawal->merchant_id)->lockForUpdate()->firstOrFail();

                if ($wallet->balance < $lockedWithdrawal->amount) {
                    throw new \DomainException('Saldo merchant tidak mencukupi.');
                }

                $lockedWithdrawal->update([
                    'status' => 'processing',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'payout_reference' => 'WD-'.$lockedWithdrawal->id,
                    'payout_attempted_at' => now(),
                ]);

                return $lockedWithdrawal->fresh(['bankAccount']);
            });
        } catch (\DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        // Never hold database locks while performing an external payout request.
        try {
            $result = $payoutService->process($withdrawal);
        } catch (\Throwable $exception) {
            report($exception);
            $result = [
                'status' => 'failed',
                'payout_status' => 'configuration_or_transport_error',
                'response' => ['message' => 'Payout tidak dapat dikirim. Periksa konfigurasi dan activation produk payout.'],
            ];
        }

        $withdrawal = $settlement->recordProviderResult($withdrawal->id, $result);

        return back()->with(
            'success',
            $withdrawal->status === 'paid' ? 'Payout terkonfirmasi dan saldo telah didebit.' : 'Withdrawal sedang diproses dengan status: '.$withdrawal->status
        );

    }

    public function reject(
        Request $request,
        Withdrawal $withdrawal
    ) {

        $request->validate(['note' => ['nullable', 'string', 'max:1000']]);

        try {
            DB::transaction(function () use ($withdrawal, $request) {
                $lockedWithdrawal = Withdrawal::whereKey($withdrawal->id)->lockForUpdate()->firstOrFail();

                if ($lockedWithdrawal->status !== 'pending') {
                    throw new \DomainException('Withdrawal sudah diproses.');
                }

                $lockedWithdrawal->update([
                    'status' => 'rejected',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'note' => $request->note,
                ]);
            });
        } catch (\DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with(
            'success',
            'Penarikan ditolak'
        );

    }
}
