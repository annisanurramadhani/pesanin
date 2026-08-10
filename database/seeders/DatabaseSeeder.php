<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Super Admin Utama Platform
        User::create([
            'name' => 'Super Admin PesanIn',
            'email' => 'admin@pesanin.id',
            'password' => Hash::make('admin123'), // Ganti dengan password aman kamu
            'role' => 'super_admin',
            'merchant_id' => null,
        ]);
    }
}
