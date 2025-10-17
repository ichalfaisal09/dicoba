<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoalOpsiJawaban extends Model
{
    use HasFactory;

    protected $table = 'soal_opsi_jawaban';

    protected $fillable = [
        'soal_id',
        'huruf_opsi',
        'teks_opsi',
        'skor_opsi',
    ];

    protected $casts = [
        'soal_id' => 'integer',
        'skor_opsi' => 'float',
    ];

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SoalPertanyaan::class, 'soal_id');
    }
}
