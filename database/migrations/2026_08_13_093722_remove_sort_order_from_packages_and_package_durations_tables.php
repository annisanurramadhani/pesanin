<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('package_durations', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0);
        });

        Schema::table('package_durations', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0);
        });
    }
};
