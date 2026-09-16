<?php

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        Merchant::updateOrCreate(
            [
                'slug' => 'warung-makan-barokah',
            ],
            [
                'name' => 'Warung Makan Barokah',
                'phone' => '081234567890',
                'address' => 'Jl. Veteran No. 10, Purwakarta',
                'logo' => null,
                'status' => 'active',
                'latitude' => -6.5569,
                'longitude' => 107.4421,
            ]
        );

        Merchant::updateOrCreate(
            [
                'slug' => 'kedai-nusantara',
            ],
            [
                'name' => 'Kedai Nusantara',
                'phone' => '081298765432',
                'address' => 'Jl. KK Singawinata No. 25, Purwakarta',
                'logo' => null,
                'status' => 'active',
                'latitude' => -6.5547,
                'longitude' => 107.4438,
            ]
        );

        $this->command->info(
            'Merchant berhasil dibuat/diperbarui.'
        );
    }
}
