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
        Schema::create('soal_opsi_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_id')
                ->constrained('soal_pertanyaan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->char('huruf_opsi', 1);
            $table->text('teks_opsi');
            $table->decimal('skor_opsi', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['soal_id', 'huruf_opsi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_opsi_jawaban');
    }
};
