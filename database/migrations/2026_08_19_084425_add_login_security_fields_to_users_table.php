<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Jumlah percobaan login dengan password salah
            $table->unsignedTinyInteger('failed_login_attempts')
                ->default(0)
                ->after('status');

            // Waktu sampai akun selesai dikunci
            $table->timestamp('login_locked_until')
                ->nullable()
                ->after('failed_login_attempts');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'failed_login_attempts',
                'login_locked_until',
            ]);

        });
    }
};
