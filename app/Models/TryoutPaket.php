<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TryoutPaket extends Model
{
    use HasFactory;

    protected $table = 'tryout_paket';

    protected $fillable = [
        'konfigurasi_dasar_sistem_id',
        'nama',
        'waktu_pengerjaan',
        'harga',
        'is_aktif',
    ];

    protected $casts = [
        'waktu_pengerjaan' => 'integer',
        'harga' => 'integer',
    ];

    public function konfigurasi(): BelongsTo
    {
        return $this->belongsTo(KonfigurasiDasarSistem::class, 'konfigurasi_dasar_sistem_id');
    }
}
