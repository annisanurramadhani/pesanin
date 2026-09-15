<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\MerchantSetting;
use Illuminate\Database\Seeder;

class MerchantSettingSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {

            MerchantSetting::updateOrCreate(
                [
                    'merchant_id' => $merchant->id,
                ],
                [
                    'cs_phone' => $merchant->phone,
                    'description' => 'Melayani pesanan makanan dan minuman.',
                ]
            );
        }

        $this->command->info(
            'Merchant setting berhasil dibuat/diperbarui.'
        );
    }
}
