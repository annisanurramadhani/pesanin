<?php

namespace App\Services;

use App\Models\MerchantWallet;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class WithdrawalSettlementService
{
    /**
     * Persist a provider outcome and debit the wallet only once the outcome is
     * confirmed paid. This method is safe to call repeatedly for a payout.
     */
    public function recordProviderResult(int $withdrawalId, array $result): Withdrawal
    {
        return DB::transaction(function () use ($withdrawalId, $result) {
            $withdrawal = Withdrawal::lockForUpdate()->findOrFail($withdrawalId);

            if ($withdrawal->status !== 'processing') {
                return $withdrawal;
            }

            $withdrawal->update([
                'payout_id' => $result['payout_id'] ?? $withdrawal->payout_id,
                'payout_status' => $result['payout_status'] ?? null,
                'payout_response' => $result['response'] ?? null,
            ]);

            $status = $result['status'] ?? 'processing';

            if ($status === 'failed') {
                $withdrawal->update(['status' => 'failed']);

                return $withdrawal->fresh();
            }

            if ($status !== 'paid') {
                return $withdrawal->fresh();
            }

            $wallet = MerchantWallet::where('merchant_id', $withdrawal->merchant_id)
                ->lockForUpdate()
                ->firstOrFail();

            $transaction = WalletTransaction::firstOrCreate(
                [
                    'reference_type' => Withdrawal::class,
                    'reference_id' => $withdrawal->id,
                    'type' => 'debit',
                ],
                [
                    'merchant_id' => $withdrawal->merchant_id,
                    'status' => 'success',
                    'amount' => $withdrawal->amount,
                    'description' => 'Penarikan saldo merchant',
                ]
            );

            if ($transaction->wasRecentlyCreated) {
                if ($wallet->balance < $withdrawal->amount) {
                    throw new \LogicException('Saldo merchant tidak mencukupi untuk penyelesaian withdrawal.');
                }

                $wallet->decrement('balance', $withdrawal->amount);
            }

            $withdrawal->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            return $withdrawal->fresh();
        });
    }

    public function recordProviderResultByPayoutId(string $payoutId, array $result): ?Withdrawal
    {
        $withdrawalId = Withdrawal::where('payout_id', $payoutId)->value('id');

        return $withdrawalId
            ? $this->recordProviderResult($withdrawalId, $result)
            : null;
    }
}
