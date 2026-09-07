<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item_units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->cascadeOnDelete();

            $table->unsignedInteger('unit_number');

            $table->string('status')
                ->default('pending');

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'order_item_id',
                'unit_number',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_units');
    }
};
