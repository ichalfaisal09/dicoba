<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KonfigurasiDasarSistem extends Model
{
    use HasFactory;

    protected $table = 'konfigurasi_dasar_sistem';

    protected $fillable = [
        'subtes_id',
        'nama',
        'jumlah_soal',
        'urutan',
        'nilai_minimal',
    ];

    public function subtes(): BelongsTo
    {
        return $this->belongsTo(KategoriSubtes::class, 'subtes_id');
    }

    public function paket(): BelongsToMany
    {
        return $this->belongsToMany(TryoutPaket::class, 'konfigurasi_ke_tryout', 'konfigurasi_dasar_sistem_id', 'tryout_paket_id')
            ->withPivot('urutan')
            ->withTimestamps();
    }
}
