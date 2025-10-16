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
        Schema::create('kategori_variasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('materi_id')->constrained('kategori_materi')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_variasi');
    }
};
