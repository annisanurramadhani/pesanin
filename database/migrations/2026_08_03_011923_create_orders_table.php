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
        Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
        $table->foreignId('qr_code_id')->nullable()->constrained('qr_codes')->nullOnDelete();
        $table->string('order_number')->unique();
        $table->string('customer_name')->nullable();
        $table->decimal('total_amount', 12, 2);
        $table->enum('status', ['menunggu', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
