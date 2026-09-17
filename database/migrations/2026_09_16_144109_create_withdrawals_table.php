<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {

            $table->id();

            $table->foreignId('merchant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('merchant_bank_account_id')
                ->constrained('merchant_bank_accounts')
                ->cascadeOnDelete();

            $table->decimal(
                'amount',
                12,
                2
            );

            $table->enum(
                'status',
                [
                    'pending',
                    'approved',
                    'rejected',
                    'paid'
                ]
            )
            ->default('pending');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamp('paid_at')
                ->nullable();

            $table->text('note')
                ->nullable();

            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};