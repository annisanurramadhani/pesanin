
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchant_bank_accounts', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'active',
                'inactive',
                'rejected',
            ])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('merchant_bank_accounts', function (Blueprint $table) {
            $table->enum('status', [
                'active',
                'inactive',
                'pending',
                'rejected',
            ])->default('active')->change();
        });
    }
};
