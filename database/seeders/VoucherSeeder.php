<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $merchant = Merchant::first();

        if (!$merchant) {
            $this->command->warn(
                'Tidak ada merchant. VoucherSeeder dibatalkan.'
            );

            return;
        }

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

                'starts_at' => now()->startOfDay(),

                'expires_at' => now()
                    ->addDays(30)
                    ->endOfDay(),

                'status' => 'active',
            ]
        );

        $this->command->info(
            'Voucher HEMAT10 berhasil dibuat/diperbarui.'
        );
    }
}