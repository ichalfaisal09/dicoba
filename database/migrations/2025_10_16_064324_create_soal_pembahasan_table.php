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
        Schema::create('soal_pembahasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_id')->constrained('soal_pertanyaan')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('teks_pembahasan')->nullable();
            $table->string('referensi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_pembahasan');
    }
};
