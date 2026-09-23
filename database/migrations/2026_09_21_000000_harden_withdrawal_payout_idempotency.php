<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->string('payout_reference')->nullable()->unique()->after('payout_id');
            $table->timestamp('payout_attempted_at')->nullable()->after('payout_response');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->unique(
                ['reference_type', 'reference_id', 'type'],
                'wallet_transactions_reference_type_id_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropUnique('wallet_transactions_reference_type_id_type_unique');
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropUnique(['payout_reference']);
            $table->dropColumn(['payout_reference', 'payout_attempted_at']);
        });
    }
};
