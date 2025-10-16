<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriVariasi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kategori_variasi';

    protected $fillable = [
        'kode',
        'materi_id',
        'nama',
        'deskripsi',
    ];

    public function materi(): BelongsTo
    {
        return $this->belongsTo(KategoriMateri::class, 'materi_id');
    }

    public function subtes(): HasOneThrough
    {
        return $this->hasOneThrough(
            KategoriSubtes::class,
            KategoriMateri::class,
            'id',
            'id',
            'materi_id',
            'subtes_id'
        );
    }
}
