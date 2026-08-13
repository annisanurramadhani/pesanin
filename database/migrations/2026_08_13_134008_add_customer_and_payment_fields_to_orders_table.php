<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Customer information
            $table->string('customer_name')
                ->nullable()
                ->after('order_number');

            $table->string('customer_phone')
                ->nullable()
                ->after('customer_name');

            $table->string('customer_email')
                ->nullable()
                ->after('customer_phone');

            // Payment
            $table->enum('payment_method', [
                'qris',
                'cash',
            ])
                ->default('cash')
                ->after('total');

            $table->string('payment_provider')
                ->nullable()
                ->after('payment_method');

            // Receipt
            $table->timestamp('receipt_sent_at')
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'customer_phone',
                'customer_email',
                'payment_method',
                'payment_provider',
                'receipt_sent_at',
            ]);
        });
    }
};
