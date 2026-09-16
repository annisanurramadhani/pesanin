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
        Schema::create('customers', function (Blueprint $table) {

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | Token anonim dari browser customer
            |--------------------------------------------------------------------------
            |
            | Token ini disimpan di localStorage browser.
            | Bukan untuk login, hanya mengenali customer lama.
            |
            */

            $table->uuid('customer_token')
                ->unique();


            /*
            |--------------------------------------------------------------------------
            | Data customer
            |--------------------------------------------------------------------------
            */

            $table->string('name');


            $table->string('phone')
                ->nullable();


            $table->string('email')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Statistik sederhana
            |--------------------------------------------------------------------------
            */

            $table->timestamp('last_order_at')
                ->nullable();


            $table->timestamps();

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
