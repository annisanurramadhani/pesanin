<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah data role lama 'staff' menjadi 'kasir'
        DB::statement("
            UPDATE users
            SET role = 'kasir'
            WHERE role = 'staff'
        ");

        // Ubah enum role dengan menghapus 'staff'
        // dan menambahkan 'kasir' serta 'dapur'
        DB::statement("
            ALTER TABLE users
            MODIFY role ENUM(
                'super_admin',
                'owner',
                'kasir',
                'dapur'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        // Kembalikan kasir menjadi staff sebelum enum dikembalikan
        DB::statement("
            UPDATE users
            SET role = 'staff'
            WHERE role IN ('kasir', 'dapur')
        ");

        // Kembalikan struktur enum seperti sebelumnya
        DB::statement("
            ALTER TABLE users
            MODIFY role ENUM(
                'super_admin',
                'owner',
                'staff'
            ) NOT NULL
            DEFAULT 'staff'
        ");
    }
};
