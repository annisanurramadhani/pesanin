<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_settings');
    }
};