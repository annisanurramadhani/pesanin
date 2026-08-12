<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Silver Package
        |--------------------------------------------------------------------------
        */

        $silver = Package::updateOrCreate(
            [
                'slug' => 'silver',
            ],
            [
                'name' => 'Silver',
                'description' => 'Silver package for merchants who are just getting started.',
                'badge' => null,
                'status' => 'active',
                'sort_order' => 1,
            ]
        );

        $silver->durations()->updateOrCreate(
            [
                'name' => 'Monthly',
            ],
            [
                'duration_days' => 30,
                'price' => 75000,
                'discount_price' => null,
                'sort_order' => 1,
                'status' => 'active',
            ]
        );

        $silver->durations()->updateOrCreate(
            [
                'name' => 'Quarterly',
            ],
            [
                'duration_days' => 90,
                'price' => 200000,
                'discount_price' => 180000,
                'sort_order' => 2,
                'status' => 'active',
            ]
        );

        $silver->durations()->updateOrCreate(
            [
                'name' => 'Yearly',
            ],
            [
                'duration_days' => 365,
                'price' => 750000,
                'discount_price' => 650000,
                'sort_order' => 3,
                'status' => 'active',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Gold Package
        |--------------------------------------------------------------------------
        */

        $gold = Package::updateOrCreate(
            [
                'slug' => 'gold',
            ],
            [
                'name' => 'Gold',
                'description' => 'Gold package for growing merchants who need more features.',
                'badge' => 'Popular',
                'status' => 'active',
                'sort_order' => 2,
            ]
        );

        $gold->durations()->updateOrCreate(
            [
                'name' => 'Monthly',
            ],
            [
                'duration_days' => 30,
                'price' => 150000,
                'discount_price' => null,
                'sort_order' => 1,
                'status' => 'active',
            ]
        );

        $gold->durations()->updateOrCreate(
            [
                'name' => 'Quarterly',
            ],
            [
                'duration_days' => 90,
                'price' => 400000,
                'discount_price' => 350000,
                'sort_order' => 2,
                'status' => 'active',
            ]
        );

        $gold->durations()->updateOrCreate(
            [
                'name' => 'Yearly',
            ],
            [
                'duration_days' => 365,
                'price' => 1500000,
                'discount_price' => 1200000,
                'sort_order' => 3,
                'status' => 'active',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Platinum Package
        |--------------------------------------------------------------------------
        */

        $platinum = Package::updateOrCreate(
            [
                'slug' => 'platinum',
            ],
            [
                'name' => 'Platinum',
                'description' => 'Platinum package for established merchants who need the most advanced features.',
                'badge' => 'Best Value',
                'status' => 'active',
                'sort_order' => 3,
            ]
        );

        $platinum->durations()->updateOrCreate(
            [
                'name' => 'Monthly',
            ],
            [
                'duration_days' => 30,
                'price' => 250000,
                'discount_price' => null,
                'sort_order' => 1,
                'status' => 'active',
            ]
        );

        $platinum->durations()->updateOrCreate(
            [
                'name' => 'Quarterly',
            ],
            [
                'duration_days' => 90,
                'price' => 700000,
                'discount_price' => 625000,
                'sort_order' => 2,
                'status' => 'active',
            ]
        );

        $platinum->durations()->updateOrCreate(
            [
                'name' => 'Yearly',
            ],
            [
                'duration_days' => 365,
                'price' => 2500000,
                'discount_price' => 2100000,
                'sort_order' => 3,
                'status' => 'active',
            ]
        );
    }
}
