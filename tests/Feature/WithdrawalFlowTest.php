<?php

namespace Tests\Feature;

use App\Http\Controllers\Merchant\WithdrawalController as MerchantWithdrawalController;
use App\Http\Controllers\SuperAdmin\WithdrawalController as SuperAdminWithdrawalController;
use App\Models\Merchant;
use App\Models\MerchantBankAccount;
use App\Models\MerchantWallet;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use App\Services\MidtransPayoutService;
use App\Services\WithdrawalSettlementService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class WithdrawalFlowTest extends TestCase
{
    // Keep each test isolated without running migrate:fresh or deleting data.
    use DatabaseTransactions;

    public function test_merchant_can_create_one_pending_withdrawal_without_debiting_wallet(): void
    {
        [$merchant, $user, $wallet] = $this->merchantContext(500000);

        $this->withdraw($user, 100000);

        $this->assertDatabaseHas('withdrawals', ['merchant_id' => $merchant->id, 'amount' => 100000, 'status' => 'pending']);
        $this->assertSame('500000.00', $wallet->fresh()->balance);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_merchant_cannot_create_a_second_active_withdrawal(): void
    {
        [, $user] = $this->merchantContext(500000);
        $this->withdraw($user, 100000);
        $this->withdraw($user, 100000);

        $this->assertDatabaseCount('withdrawals', 1);
    }

    public function test_failed_and_processing_payouts_do_not_debit_wallet(): void
    {
        [$merchant, , $wallet] = $this->merchantContext(500000);
        $admin = User::factory()->create(['merchant_id' => null, 'role' => 'super_admin', 'status' => 'active']);
        $this->actingAs($admin);
        $controller = app(SuperAdminWithdrawalController::class);
        $payoutService = app(MidtransPayoutService::class);
        $settlement = app(WithdrawalSettlementService::class);
        $failed = $this->pendingWithdrawal($merchant, 100000);

        config(['services.midtrans.payout_enabled' => false, 'services.midtrans.payout_simulation_status' => 'failed']);
        $controller->approve($failed, $payoutService, $settlement);
        $this->assertSame('failed', $failed->fresh()->status);
        $this->assertSame('500000.00', $wallet->fresh()->balance);

        $processing = $this->pendingWithdrawal($merchant, 100000);
        config(['services.midtrans.payout_simulation_status' => 'processing']);
        $controller->approve($processing, $payoutService, $settlement);
        $this->assertSame('processing', $processing->fresh()->status);
        $this->assertSame('500000.00', $wallet->fresh()->balance);
    }

    public function test_paid_payout_is_idempotent_and_creates_one_debit(): void
    {
        [$merchant, , $wallet] = $this->merchantContext(500000);
        $withdrawal = $this->processingWithdrawal($merchant, 100000);
        $service = app(WithdrawalSettlementService::class);
        $result = ['status' => 'paid', 'payout_status' => 'simulation_paid', 'payout_id' => 'SANDBOX-WD-'.$withdrawal->id];

        $service->recordProviderResult($withdrawal->id, $result);
        $service->recordProviderResult($withdrawal->id, $result);

        $this->assertSame('paid', $withdrawal->fresh()->status);
        $this->assertSame('400000.00', $wallet->fresh()->balance);
        $this->assertSame(1, WalletTransaction::where('reference_type', Withdrawal::class)->where('reference_id', $withdrawal->id)->where('type', 'debit')->count());
    }

    public function test_admin_approve_simulation_pays_once_and_cannot_be_approved_again(): void
    {
        [$merchant, , $wallet] = $this->merchantContext(500000);
        $withdrawal = $this->pendingWithdrawal($merchant, 100000);
        $admin = User::factory()->create(['merchant_id' => null, 'role' => 'super_admin', 'status' => 'active']);
        $this->actingAs($admin);
        config(['services.midtrans.payout_enabled' => false, 'services.midtrans.payout_simulation_status' => 'paid']);

        $controller = app(SuperAdminWithdrawalController::class);
        $controller->approve($withdrawal, app(MidtransPayoutService::class), app(WithdrawalSettlementService::class));
        $controller->approve($withdrawal->fresh(), app(MidtransPayoutService::class), app(WithdrawalSettlementService::class));

        $this->assertSame('paid', $withdrawal->fresh()->status);
        $this->assertSame('400000.00', $wallet->fresh()->balance);
        $this->assertDatabaseCount('wallet_transactions', 1);
    }

    public function test_admin_rejection_keeps_wallet_balance_unchanged(): void
    {
        [$merchant, , $wallet] = $this->merchantContext(500000);
        $withdrawal = $this->pendingWithdrawal($merchant, 100000);
        $this->actingAs(User::factory()->create(['merchant_id' => null, 'role' => 'super_admin', 'status' => 'active']));
        $request = Request::create('/super_admin/withdrawals/'.$withdrawal->id.'/reject', 'POST', ['note' => 'Dokumen belum lengkap']);

        app(SuperAdminWithdrawalController::class)->reject($request, $withdrawal);

        $this->assertSame('rejected', $withdrawal->fresh()->status);
        $this->assertSame('Dokumen belum lengkap', $withdrawal->fresh()->note);
        $this->assertSame('500000.00', $wallet->fresh()->balance);
    }

    public function test_insufficient_balance_cannot_be_requested(): void
    {
        [$merchant, $user] = $this->merchantContext(50000);
        $this->withdraw($user, 100000);

        $this->assertDatabaseMissing('withdrawals', ['merchant_id' => $merchant->id]);
    }

    private function merchantContext(int $balance): array
    {
        $merchant = Merchant::create(['name' => 'Test Merchant', 'slug' => 'merchant-'.fake()->unique()->uuid]);
        $user = User::factory()->create(['merchant_id' => $merchant->id, 'role' => 'owner', 'status' => 'active']);
        $wallet = MerchantWallet::create(['merchant_id' => $merchant->id, 'balance' => $balance]);
        MerchantBankAccount::create([
            'merchant_id' => $merchant->id,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Test Merchant',
            'status' => 'active',
            'is_locked' => true,
        ]);

        return [$merchant, $user, $wallet];
    }

    private function withdraw(User $user, int $amount): void
    {
        $request = Request::create('/merchant/withdrawals', 'POST', ['amount' => $amount]);
        $request->setUserResolver(fn () => $user);

        app(MerchantWithdrawalController::class)->store($request);
    }

    private function processingWithdrawal(Merchant $merchant, int $amount): Withdrawal
    {
        return Withdrawal::create([
            'merchant_id' => $merchant->id,
            'merchant_bank_account_id' => $merchant->bankAccount->id,
            'amount' => $amount,
            'status' => 'processing',
        ]);
    }

    private function pendingWithdrawal(Merchant $merchant, int $amount): Withdrawal
    {
        return Withdrawal::create([
            'merchant_id' => $merchant->id,
            'merchant_bank_account_id' => $merchant->bankAccount->id,
            'amount' => $amount,
            'status' => 'pending',
        ]);
    }
}
