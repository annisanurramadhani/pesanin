<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {

            Voucher::updateOrCreate(
                [
                    'merchant_id' => $merchant->id,
                    'code' => 'HEMAT10',
                ],
                [
                    'type' => 'percentage',
                    'value' => 10,
                    'min_order_amount' => 30000,
                    'max_discount_amount' => 15000,
                    'usage_limit' => 100,
                    'used_count' => 0,
                    'starts_at' => now()->startOfDay(),
                    'expires_at' => now()->addDays(30)->endOfDay(),
                    'status' => 'active',
                ]
            );

            Voucher::updateOrCreate(
                [
                    'merchant_id' => $merchant->id,
                    'code' => 'POTONG5000',
                ],
                [
                    'type' => 'fixed',
                    'value' => 5000,
                    'min_order_amount' => 25000,
                    'max_discount_amount' => null,
                    'usage_limit' => 50,
                    'used_count' => 0,
                    'starts_at' => now()->startOfDay(),
                    'expires_at' => now()->addDays(30)->endOfDay(),
                    'status' => 'active',
                ]
            );
        }

        $this->command->info(
            'Voucher berhasil dibuat/diperbarui.'
        );
    }
}
