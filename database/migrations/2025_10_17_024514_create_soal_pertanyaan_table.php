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
            $table->foreignId('variasi_id')
                ->constrained('kategori_variasi')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('kode_soal')->unique();
            $table->text('teks_soal');
            $table->enum('tingkat_kesulitan', ['mudah', 'sedang', 'sulit'])->default('sedang')->index();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->index();
            $table->timestamps();
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
