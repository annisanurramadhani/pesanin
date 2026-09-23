<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MidtransPayoutService
{
    public function process($withdrawal): array
    {
        if (! config('services.midtrans.payout_enabled') || config('services.midtrans.payout_mode') === 'simulation') {
            return $this->sandboxSimulation($withdrawal);
        }

        return $this->sendToMidtrans($withdrawal);
    }

    private function sandboxSimulation($withdrawal): array
    {
        $status = config('services.midtrans.payout_simulation_status', 'paid');

        if (! in_array($status, ['paid', 'processing', 'failed'], true)) {
            throw new \InvalidArgumentException('MIDTRANS_PAYOUT_SIMULATION_STATUS harus paid, processing, atau failed.');
        }

        Log::info('PesanIn sandbox payout simulation', [
            'withdrawal_id' => $withdrawal->id,
            'merchant_id' => $withdrawal->merchant_id,
            'amount' => $withdrawal->amount,
            'bank' => $withdrawal->bankAccount?->bank_name,
        ]);

        return [
            'status' => $status,
            'payout_status' => 'simulation_'.$status,

            /*
             * Ini BUKAN payout ID Midtrans asli.
             * Hanya ID simulasi internal.
             */
            'payout_id' => 'SANDBOX-WD-'.$withdrawal->id.'-'.time(),

            'response' => [
                'message' => 'Local payout simulation; this is not a Midtrans payout.',
                'withdrawal_id' => $withdrawal->id,
                'amount' => $withdrawal->amount,
            ],
        ];
    }

    private function sendToMidtrans($withdrawal): array
    {
        throw new \RuntimeException(
            'Payout Midtrans live belum dapat dijalankan: endpoint, credential, callback signature, dan activation produk resmi belum dikonfigurasi.'
        );
    }
}
