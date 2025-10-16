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
        Schema::create('konfigurasi_dasar_sistem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subtes_id')->constrained('kategori_subtes')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama');
            $table->unsignedInteger('jumlah_soal');
            $table->unsignedTinyInteger('urutan');
            $table->unsignedInteger('nilai_minimal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_dasar_sistem');
    }
};
