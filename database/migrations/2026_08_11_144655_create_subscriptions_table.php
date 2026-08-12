<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            $table->foreignId('package_duration_id')
                ->constrained('package_durations')
                ->restrictOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            $table->decimal('price', 15, 2);

            $table->enum('status', [
                'active',
                'expired',
                'cancelled',
            ])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
