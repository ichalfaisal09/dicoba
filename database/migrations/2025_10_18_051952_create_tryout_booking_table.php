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
        Schema::create('tryout_booking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tryout_paket_id')->constrained('tryout_paket')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('tanggal_mulai')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->unsignedInteger('durasi_menit')->nullable();
            $table->unsignedInteger('harga')->nullable();
            $table->string('kode_pembayaran')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tryout_paket_id'], 'tryout_booking_unique_user_paket');
            $table->index(['status', 'tanggal_mulai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryout_booking');
    }
};
