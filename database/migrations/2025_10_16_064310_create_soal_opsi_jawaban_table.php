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
            $table->foreignId('soal_id')->constrained('soal_pertanyaan')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('huruf', ['A', 'B', 'C', 'D', 'E']);
            $table->unsignedTinyInteger('urutan');
            $table->text('teks_opsi');
            $table->decimal('bobot_opsi', 8, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
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
