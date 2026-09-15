<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\PackageDuration;
use App\Models\Subscription;
use App\Models\SubscriptionPromotion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $promotion = SubscriptionPromotion::where(
            'name',
            'Promo Spesial PesanIn'
        )->first();

        $monthly = PackageDuration::where('name', 'Monthly')
            ->whereHas('package', function ($query) {
                $query->where('slug', 'gold');
            })
            ->first();

        if (!$monthly) {
            $this->command->warn(
                'Gold Monthly tidak ditemukan. Jalankan PackageSeeder terlebih dahulu.'
            );

            return;
        }

        $priceData = $monthly->getSubscriptionPrice();

        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {

            $subscription = Subscription::firstOrNew([
                'merchant_id' => $merchant->id,
                'package_duration_id' => $monthly->id,
            ]);

            // Buat invoice hanya jika subscription baru
            if (!$subscription->exists) {
                $subscription->invoice_number =
                    'SUB-' .
                    now()->format('YmdHis') .
                    '-' .
                    strtoupper(Str::random(6));
            }

            $subscription->promotion_id = $promotion?->id;

            $subscription->start_date = now()->startOfDay();

            $subscription->end_date = now()
                ->addDays($monthly->duration_days)
                ->startOfDay();

            $subscription->price = $priceData['final_price'];

            $subscription->paid_at = now();

            $subscription->status = 'active';

            $subscription->save();
        }

        $this->command->info(
            'Subscription berhasil dibuat/diperbarui.'
        );
    }
}
