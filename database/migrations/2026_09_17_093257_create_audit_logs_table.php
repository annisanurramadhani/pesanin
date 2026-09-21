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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // user yang melakukan aksi
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // jenis aksi crate, update, delete, login, logout, dll
            $table->string('action');

            // nama tabel/model
            $table->string('model_type')
                ->nullable();

            // id data yang berubah
            $table->unsignedBigInteger('model_id')
                ->nullable();

            // data sebelum perubahan
            $table->json('old_values')
                ->nullable();

            // data sesudah perubahan
            $table->json('new_values')
                ->nullable();

            // informasi tambahan
            $table->ipAddress('ip_address')
                ->nullable();

            $table->text('user_agent')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
