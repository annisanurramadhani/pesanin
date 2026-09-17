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
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();

            // General
            $table->string('website_name')->default('PesanIn');
            $table->string('tagline')->nullable();

            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();


            // Hero Landing Page

            $table->string('hero_badge')->nullable();

            $table->string('hero_content')->nullable();

            $table->text('hero_description')->nullable();


            // CTA

            $table->string('cta_content')->nullable();

            $table->text('cta_description')->nullable();

            $table->string('cta_button_text')->nullable();



            // Footer

            $table->string('footer_text')->nullable();

            $table->string('footer_email')->nullable();

            $table->string('footer_whatsapp')->nullable();

            // Social Media

            $table->string('instagram_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
