<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {

            $table->string('payout_id')
                ->nullable()
                ->after('status');

            $table->string('payout_status')
                ->nullable()
                ->after('payout_id');

            $table->json('payout_response')
                ->nullable()
                ->after('payout_status');

        });
    }


    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {

            $table->dropColumn([
                'payout_id',
                'payout_status',
                'payout_response'
            ]);

        });
    }
};