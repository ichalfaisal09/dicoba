<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SoalPertanyaan extends Model
{
    use HasFactory;

    protected $table = 'soal_pertanyaan';

    protected $fillable = [
        'variasi_id',
        'kode_soal',
        'teks_soal',
        'tingkat_kesulitan',
        'status',
    ];

    protected $casts = [
        'variasi_id' => 'integer',
    ];

    public function variasi(): BelongsTo
    {
        return $this->belongsTo(KategoriVariasi::class, 'variasi_id');
    }

    public function opsiJawaban(): HasMany
    {
        return $this->hasMany(SoalOpsiJawaban::class, 'soal_id');
    }

    public function pembahasan(): HasOne
    {
        return $this->hasOne(SoalPembahasan::class, 'soal_id');
    }
}
