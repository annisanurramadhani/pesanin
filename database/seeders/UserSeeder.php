<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'admin@pesanin.id'],
            [
                'merchant_id' => null,
                'name' => 'Super Admin PesanIn',
                'password' => 'password',
                'role' => 'super_admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'verification_code' => null,
                'verification_code_expires_at' => null,
                'failed_login_attempts' => 0,
                'login_locked_until' => null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Ambil Merchant
        |--------------------------------------------------------------------------
        */

        $barokah = Merchant::where(
            'slug',
            'warung-makan-barokah'
        )->firstOrFail();

        $nusantara = Merchant::where(
            'slug',
            'kedai-nusantara'
        )->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Warung Makan Barokah
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'owner@pesanin.id'],
            [
                'merchant_id' => $barokah->id,
                'name' => 'Owner Barokah',
                'password' => 'password',
                'role' => 'owner',
                'status' => 'active',
                'email_verified_at' => now(),
                'verification_code' => null,
                'verification_code_expires_at' => null,
                'failed_login_attempts' => 0,
                'login_locked_until' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@pesanin.id'],
            [
                'merchant_id' => $barokah->id,
                'name' => 'Kasir Barokah',
                'password' => 'password',
                'role' => 'kasir',
                'status' => 'active',
                'email_verified_at' => now(),
                'verification_code' => null,
                'verification_code_expires_at' => null,
                'failed_login_attempts' => 0,
                'login_locked_until' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dapur@pesanin.id'],
            [
                'merchant_id' => $barokah->id,
                'name' => 'Dapur Barokah',
                'password' => 'password',
                'role' => 'dapur',
                'status' => 'active',
                'email_verified_at' => now(),
                'verification_code' => null,
                'verification_code_expires_at' => null,
                'failed_login_attempts' => 0,
                'login_locked_until' => null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Kedai Nusantara
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'owner2@pesanin.id'],
            [
                'merchant_id' => $nusantara->id,
                'name' => 'Owner Nusantara',
                'password' => 'password',
                'role' => 'owner',
                'status' => 'active',
                'email_verified_at' => now(),
                'verification_code' => null,
                'verification_code_expires_at' => null,
                'failed_login_attempts' => 0,
                'login_locked_until' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir2@pesanin.id'],
            [
                'merchant_id' => $nusantara->id,
                'name' => 'Kasir Nusantara',
                'password' => 'password',
                'role' => 'kasir',
                'status' => 'active',
                'email_verified_at' => now(),
                'verification_code' => null,
                'verification_code_expires_at' => null,
                'failed_login_attempts' => 0,
                'login_locked_until' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dapur2@pesanin.id'],
            [
                'merchant_id' => $nusantara->id,
                'name' => 'Dapur Nusantara',
                'password' => 'password',
                'role' => 'dapur',
                'status' => 'active',
                'email_verified_at' => now(),
                'verification_code' => null,
                'verification_code_expires_at' => null,
                'failed_login_attempts' => 0,
                'login_locked_until' => null,
            ]
        );

        $this->command->info('User berhasil dibuat/diperbarui.');
    }
}
