<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();

            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('slug');

            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->decimal('price', 15, 2);
            $table->enum('status', [
                'available',
                'unavailable',
            ])->default('available');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['merchant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
