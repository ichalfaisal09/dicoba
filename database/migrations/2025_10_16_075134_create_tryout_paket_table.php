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
        Schema::create('tryout_paket', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('konfigurasi_dasar_sistem_id')->constrained('konfigurasi_dasar_sistem')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedInteger('waktu_pengerjaan');
            $table->unsignedInteger('harga');
            $table->enum('is_aktif', ['aktif', 'nonaktif'])->default('aktif')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryout_paket');
    }
};
