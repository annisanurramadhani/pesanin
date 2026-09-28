<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {

            $table->string('payment_type')
                ->nullable()
                ->after('invoice_number');

            $table->string('payment_bank')
                ->nullable()
                ->after('payment_type');

            $table->string('va_number')
                ->nullable()
                ->after('payment_bank');

            $table->timestamp('expired_at')
                ->nullable()
                ->after('va_number');

            $table->string('payment_status')
                ->default('pending')
                ->after('expired_at');

        });
    }


    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {

            $table->dropColumn([
                'payment_type',
                'payment_bank',
                'va_number',
                'expired_at',
                'payment_status',
            ]);

        });
    }
};
