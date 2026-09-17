<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('wallet_transactions', function (Blueprint $table) {


            $table->id();


            $table->foreignId('merchant_id')
                ->constrained()
                ->cascadeOnDelete();


            /*
             * credit  = uang masuk
             * debit   = uang keluar
             */
            $table->enum(
                'type',
                [
                    'credit',
                    'debit'
                ]
            );


            $table->decimal(
                'amount',
                12,
                2
            );


            /*
             * contoh:
             * Order
             * Withdrawal
             */
            $table->string(
                'reference_type'
            )
            ->nullable();


            $table->unsignedBigInteger(
                'reference_id'
            )
            ->nullable();



            $table->text(
                'description'
            )
            ->nullable();



            $table->timestamps();



            $table->index([
                'reference_type',
                'reference_id'
            ]);


        });

    }



    public function down(): void
    {
        Schema::dropIfExists(
            'wallet_transactions'
        );
    }

};
