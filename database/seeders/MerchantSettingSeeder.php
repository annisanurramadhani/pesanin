<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\MerchantSetting;
use Illuminate\Database\Seeder;

class MerchantSettingSeeder extends Seeder
{
    /**
     * Menjalankan seeder merchant settings.
     */
    public function run(): void
    {
        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {

            MerchantSetting::updateOrCreate(
                [
                    'merchant_id' => $merchant->id,
                ],
                [
                    'description' => null,
                ]
            );

        }
    }
}