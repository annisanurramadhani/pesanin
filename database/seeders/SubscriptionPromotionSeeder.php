<?php

namespace Database\Seeders;

use App\Models\SubscriptionPromotion;
use Illuminate\Database\Seeder;

class SubscriptionPromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionPromotion::create([
            'name' => 'Promo Spesial PesanIn',
            'description' => 'Nikmati diskon spesial untuk berlangganan PesanIn.',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(30),
            'status' => 'active',
        ]);
    }
}