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
        Schema::create('soal_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('variasi_id')->constrained('kategori_variasi')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('teks_pertanyaan');
            $table->enum('kesulitan', ['mudah', 'sedang', 'sulit'])->default('sedang')->index();
            $table->enum('is_aktif', ['aktif', 'nonaktif'])->default('aktif')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_pertanyaan');
    }
};
