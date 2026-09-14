<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'max_qr_codes')) {
                $table->unsignedInteger('max_qr_codes')
                    ->default(0)
                    ->after('status');
            }

            if (!Schema::hasColumn('packages', 'max_menus')) {
                $table->unsignedInteger('max_menus')
                    ->default(0)
                    ->after('max_qr_codes');
            }

            if (!Schema::hasColumn('packages', 'max_staff')) {
                $table->unsignedInteger('max_staff')
                    ->default(0)
                    ->after('max_menus');
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('packages', 'max_qr_codes')) {
                $columns[] = 'max_qr_codes';
            }

            if (Schema::hasColumn('packages', 'max_menus')) {
                $columns[] = 'max_menus';
            }

            if (Schema::hasColumn('packages', 'max_staff')) {
                $columns[] = 'max_staff';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
