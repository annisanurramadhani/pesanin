<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_durations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('package_id')
                ->constrained('packages')
                ->cascadeOnDelete();

            $table->string('name');
            $table->unsignedInteger('duration_days');

            $table->decimal('price', 15, 2);

            $table->decimal('discount_price', 15, 2)->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_durations');
    }
};
