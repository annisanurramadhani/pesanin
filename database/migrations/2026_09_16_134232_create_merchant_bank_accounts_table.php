<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('merchant_bank_accounts', function (Blueprint $table) {


            $table->id();



            /*
            |--------------------------------------------------------------------------
            | MERCHANT PEMILIK REKENING
            |--------------------------------------------------------------------------
            */

            $table->foreignId('merchant_id')
                ->constrained()
                ->cascadeOnDelete();



            /*
            |--------------------------------------------------------------------------
            | DATA BANK
            |--------------------------------------------------------------------------
            */

            $table->string(
                'bank_name'
            );


            $table->string(
                'account_number'
            );


            $table->string(
                'account_name'
            );



            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            |
            | active:
            | rekening bisa digunakan withdraw
            |
            */

            $table->enum(
                'status',
                [
                    'active',
                    'inactive'
                ]
            )
            ->default('active');



            /*
            |--------------------------------------------------------------------------
            | LOCK
            |--------------------------------------------------------------------------
            |
            | Setelah dibuat merchant tidak bisa edit
            |
            */

            $table->boolean(
                'is_locked'
            )
            ->default(true);



            $table->timestamps();


        });

    }




    public function down(): void
    {

        Schema::dropIfExists(
            'merchant_bank_accounts'
        );

    }

};
