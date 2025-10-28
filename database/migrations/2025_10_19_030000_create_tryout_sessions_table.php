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
        Schema::create('tryout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_booking_id')
                ->constrained('tryout_booking')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('status')->default('in_progress')->index();
            $table->timestamp('mulai_pada')->nullable();
            $table->timestamp('selesai_pada')->nullable();
            $table->unsignedInteger('durasi_terpakai')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique('tryout_booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryout_sessions');
    }
};
