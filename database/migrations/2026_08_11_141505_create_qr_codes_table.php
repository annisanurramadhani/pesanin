<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            $table->string('name');

            $table->enum('type', [
                'menu',
                'table',
            ])->default('table');

            $table->string('code')->unique();

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
