<?php

namespace App\Http\Controllers;

use App\Services\WithdrawalSettlementService;
use Illuminate\Http\Request;

class PayoutCallbackController extends Controller
{
    /**
     * Integration hook for a payout product callback. The exact Midtrans
     * callback contract must be configured only after the product is enabled;
     * this endpoint intentionally does not assume a Midtrans payload/signature.
     */
    public function handle(Request $request, WithdrawalSettlementService $settlement)
    {
        $token = (string) config('services.midtrans.payout_callback_token');

        abort_if($token === '' || ! hash_equals($token, (string) $request->header('X-PesanIn-Payout-Token')), 403);

        $data = $request->validate([
            'payout_id' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:paid,processing,failed'],
            'payout_status' => ['nullable', 'string', 'max:255'],
            'response' => ['nullable', 'array'],
        ]);

        $withdrawal = $settlement->recordProviderResultByPayoutId($data['payout_id'], $data);

        abort_if(! $withdrawal, 404);

        return response()->json(['status' => $withdrawal->status]);
    }
}
