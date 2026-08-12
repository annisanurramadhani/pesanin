<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            $table->foreignId('qr_code_id')
                ->nullable()
                ->constrained('qr_codes')
                ->nullOnDelete();

            $table->string('order_number')->unique();

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'cancelled',
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
