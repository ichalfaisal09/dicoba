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
        Schema::create('konfigurasi_ke_tryout', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_paket_id')
                ->constrained('tryout_paket')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('konfigurasi_dasar_sistem_id')
                ->constrained('konfigurasi_dasar_sistem')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->timestamps();

            $table->unique(
                ['tryout_paket_id', 'konfigurasi_dasar_sistem_id'],
                'tryout_paket_konfigurasi_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_ke_tryout');
    }
};
