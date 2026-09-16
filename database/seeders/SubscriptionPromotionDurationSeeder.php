<?php

namespace Database\Seeders;

use App\Models\PackageDuration;
use App\Models\SubscriptionPromotion;
use Illuminate\Database\Seeder;

class SubscriptionPromotionDurationSeeder extends Seeder
{
    public function run(): void
    {
        $promotion = SubscriptionPromotion::where(
            'name',
            'Promo Spesial PesanIn'
        )->first();

        if (!$promotion) {
            $this->command->warn(
                'Promo Spesial PesanIn tidak ditemukan. Jalankan SubscriptionPromotionSeeder terlebih dahulu.'
            );

            return;
        }

        $durations = PackageDuration::whereIn('name', [
            'Monthly',
            'Quarterly',
            'Yearly',
        ])->get();

        if ($durations->isEmpty()) {
            $this->command->warn(
                'Package duration tidak ditemukan. Jalankan PackageSeeder terlebih dahulu.'
            );

            return;
        }

        $promotion->durations()->sync(
            $durations->pluck('id')->toArray()
        );

        $this->command->info(
            'Subscription promotion duration berhasil dibuat/diperbarui.'
        );
    }
}
