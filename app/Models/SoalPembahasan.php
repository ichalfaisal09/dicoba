<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoalPembahasan extends Model
{
    use HasFactory;

    protected $table = 'soal_pembahasan';

    protected $fillable = [
        'soal_id',
        'teks_pembahasan',
        'referensi',
    ];

    protected $casts = [
        'soal_id' => 'integer',
    ];

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SoalPertanyaan::class, 'soal_id');
    }
}
