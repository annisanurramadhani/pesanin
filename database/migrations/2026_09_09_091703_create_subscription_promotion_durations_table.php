<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_promotion_durations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('promotion_id')
                ->constrained('subscription_promotions')
                ->cascadeOnDelete();

            $table->foreignId('package_duration_id')
                ->constrained('package_durations')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                [
                    'promotion_id',
                    'package_duration_id',
                ],
                'promo_duration_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_promotion_durations');
    }
};
