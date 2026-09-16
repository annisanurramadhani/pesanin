<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Merchant;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {

            $makanan = Category::where('merchant_id', $merchant->id)
                ->where('slug', 'makanan')
                ->first();

            $minuman = Category::where('merchant_id', $merchant->id)
                ->where('slug', 'minuman')
                ->first();

            $snack = Category::where('merchant_id', $merchant->id)
                ->where('slug', 'snack')
                ->first();

            if (!$makanan || !$minuman || !$snack) {
                continue;
            }

            $menus = [
                [
                    'category_id' => $makanan->id,
                    'name' => 'Nasi Goreng',
                    'slug' => 'nasi-goreng',
                    'description' => 'Nasi goreng spesial dengan telur dan sayuran.',
                    'price' => 18000,
                ],
                [
                    'category_id' => $makanan->id,
                    'name' => 'Ayam Geprek',
                    'slug' => 'ayam-geprek',
                    'description' => 'Ayam crispy dengan sambal geprek.',
                    'price' => 20000,
                ],
                [
                    'category_id' => $makanan->id,
                    'name' => 'Mie Goreng',
                    'slug' => 'mie-goreng',
                    'description' => 'Mie goreng dengan telur dan sayuran.',
                    'price' => 16000,
                ],
                [
                    'category_id' => $minuman->id,
                    'name' => 'Es Teh',
                    'slug' => 'es-teh',
                    'description' => 'Es teh manis segar.',
                    'price' => 5000,
                ],
                [
                    'category_id' => $minuman->id,
                    'name' => 'Es Jeruk',
                    'slug' => 'es-jeruk',
                    'description' => 'Minuman jeruk segar.',
                    'price' => 7000,
                ],
                [
                    'category_id' => $minuman->id,
                    'name' => 'Kopi',
                    'slug' => 'kopi',
                    'description' => 'Kopi hitam hangat.',
                    'price' => 8000,
                ],
                [
                    'category_id' => $snack->id,
                    'name' => 'Pisang Goreng',
                    'slug' => 'pisang-goreng',
                    'description' => 'Pisang goreng renyah.',
                    'price' => 10000,
                ],
            ];

            foreach ($menus as $menu) {
                Menu::updateOrCreate(
                    [
                        'merchant_id' => $merchant->id,
                        'slug' => $menu['slug'],
                    ],
                    [
                        'category_id' => $menu['category_id'],
                        'name' => $menu['name'],
                        'description' => $menu['description'],
                        'image' => null,
                        'price' => $menu['price'],
                        'status' => 'available',
                    ]
                );
            }
        }

        $this->command->info(
            'Menu berhasil dibuat/diperbarui.'
        );
    }
}
