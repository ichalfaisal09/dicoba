<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TryoutSessionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'tryout_session_id',
        'soal_id',
        'jawaban_opsi_id',
        'is_flagged',
        'skor',
    ];

    protected $casts = [
        'is_flagged' => 'boolean',
        'skor' => 'float',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TryoutSession::class, 'tryout_session_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SoalPertanyaan::class, 'soal_id');
    }

    public function jawabanOpsi(): BelongsTo
    {
        return $this->belongsTo(SoalOpsiJawaban::class, 'jawaban_opsi_id');
    }
}
