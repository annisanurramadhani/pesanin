<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([

            // Master
            PackageSeeder::class,
            MerchantSeeder::class,
            UserSeeder::class,

            // Merchant data
            CategorySeeder::class,
            MenuSeeder::class,
            QrCodeSeeder::class,
            MerchantSettingSeeder::class,
            VoucherSeeder::class,

            // Subscription
            SubscriptionPromotionSeeder::class,
            SubscriptionPromotionDurationSeeder::class,
            SubscriptionSeeder::class,

            // Orders
            OrderSeeder::class,
            OrderItemSeeder::class,
            OrderItemUnitSeeder::class,
        ]);
    }
}
