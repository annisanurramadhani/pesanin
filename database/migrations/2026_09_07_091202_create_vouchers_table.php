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
    Schema::create('vouchers', function (Blueprint $table) {

        $table->id();

        $table->foreignId('merchant_id')
            ->constrained('merchants')
            ->cascadeOnDelete();

        $table->string('code', 50);

        $table->enum('type', [
            'percentage',
            'fixed',
        ]);

        $table->decimal('value', 12, 2);

        $table->decimal('min_order_amount', 12, 2)
            ->default(0);

        $table->decimal('max_discount_amount', 12, 2)
            ->nullable();

        $table->unsignedInteger('usage_limit')
            ->nullable();

        $table->unsignedInteger('used_count')
            ->default(0);

        $table->timestamp('starts_at')
            ->nullable();

        $table->timestamp('expires_at')
            ->nullable();

        $table->enum('status', [
            'active',
            'inactive',
        ])->default('active');

        $table->timestamps();

        $table->softDeletes();

        $table->unique([
            'merchant_id',
            'code',
        ]);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
