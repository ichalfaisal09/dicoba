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
        Schema::create('tryout_session_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_session_id')
                ->constrained('tryout_sessions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('soal_id')
                ->constrained('soal_pertanyaan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('jawaban_opsi_id')
                ->nullable()
                ->constrained('soal_opsi_jawaban')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->boolean('is_flagged')->default(false);
            $table->decimal('skor', 6, 2)->nullable();
            $table->timestamps();

            $table->unique(['tryout_session_id', 'soal_id'], 'session_soal_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryout_session_answers');
    }
};
