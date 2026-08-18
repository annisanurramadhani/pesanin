<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->enum('type', ['menu', 'table', 'takeaway', 'vip', 'dine_in'])
                ->default('dine_in')
                ->change();
        });

        DB::table('qr_codes')
            ->where('type', 'table')
            ->update(['type' => 'dine_in']);

        DB::table('qr_codes')
            ->where('type', 'vip')
            ->delete();

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->enum('type', ['menu', 'dine_in', 'takeaway'])
                ->default('dine_in')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->enum('type', ['menu', 'dine_in', 'takeaway', 'vip'])
                ->default('dine_in')
                ->change();
        });

        DB::table('qr_codes')
            ->where('type', 'dine_in')
            ->update(['type' => 'table']);

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->enum('type', ['menu', 'table', 'takeaway', 'vip'])
                ->default('table')
                ->change();
        });
    }
};