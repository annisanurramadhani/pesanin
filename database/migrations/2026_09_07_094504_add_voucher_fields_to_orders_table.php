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
    Schema::table('orders', function (Blueprint $table) {

        $table->foreignId('voucher_id')
            ->nullable()
            ->after('qr_code_id')
            ->constrained('vouchers')
            ->nullOnDelete();

        $table->string('voucher_code', 50)
            ->nullable()
            ->after('voucher_id');

        $table->decimal('discount', 12, 2)
            ->default(0)
            ->after('subtotal');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {

        $table->dropForeign([
            'voucher_id',
        ]);

        $table->dropColumn([
            'voucher_id',
            'voucher_code',
            'discount',
        ]);
    });
}
};
