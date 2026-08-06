<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Merchant;
use App\Models\Category;
use App\Models\Menu;
use App\Models\QrCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Dummy Merchant dulu
        $merchant = Merchant::create([
            'name' => 'Kopi PST Purwakarta',
            'slug' => 'kopi-pst-purwakarta',
            'phone' => '081234567890',
            'address' => 'Jl. Veteran No. 10, Purwakarta',
            'is_active' => true,
            'subscription_expires_at' => now()->addYear(),
        ]);

        // 2. Super Admin
        User::create([
            'name' => 'Super Admin PST',
            'email' => 'admin@pesanin.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'merchant_id' => null,
        ]);

        // 3. Owner Merchant
        User::create([
            'name' => 'Owner Kopi PST',
            'email' => 'owner@kopipst.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'merchant_id' => $merchant->id,
        ]);

        // 4. Akun Kasir
        User::create([
            'name' => 'Kasir Kopi PST',
            'email' => 'kasir@kopipst.com',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
            'merchant_id' => $merchant->id,
        ]);

        // 5. Akun Dapur
        User::create([
            'name' => 'Tim Dapur PST',
            'email' => 'dapur@kopipst.com',
            'password' => Hash::make('password123'),
            'role' => 'dapur',
            'merchant_id' => $merchant->id,
        ]);

        // 6. Sample Kategori & Menu
        $category = Category::create([
            'merchant_id' => $merchant->id,
            'name' => 'Minuman Kopi',
        ]);

        Menu::create([
            'merchant_id' => $merchant->id,
            'category_id' => $category->id,
            'name' => 'Es Kopi Susu PST',
            'description' => 'Kopi gula aren khas PST Purwakarta',
            'price' => 18000,
            'stock' => 50,
            'is_available' => true,
        ]);

        QrCode::create([
            'merchant_id' => $merchant->id,
            'name' => 'Meja 01',
            'type' => 'table',
            'code_hash' => Str::random(10),
            'is_active' => true,
        ]);
    }
}