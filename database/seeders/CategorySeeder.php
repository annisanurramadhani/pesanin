<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Merchant;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {

            $categories = [
                [
                    'name' => 'Makanan',
                    'slug' => 'makanan',
                    'description' => 'Berbagai pilihan makanan.',
                ],
                [
                    'name' => 'Minuman',
                    'slug' => 'minuman',
                    'description' => 'Berbagai pilihan minuman.',
                ],
                [
                    'name' => 'Snack',
                    'slug' => 'snack',
                    'description' => 'Aneka makanan ringan.',
                ],
            ];

            foreach ($categories as $category) {
                Category::updateOrCreate(
                    [
                        'merchant_id' => $merchant->id,
                        'slug' => $category['slug'],
                    ],
                    [
                        'name' => $category['name'],
                        'description' => $category['description'],
                        'status' => 'active',
                    ]
                );
            }
        }

        $this->command->info(
            'Category berhasil dibuat/diperbarui.'
        );
    }
}
