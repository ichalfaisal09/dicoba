<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriMateri extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kategori_materi';

    protected $fillable = [
        'kode',
        'subtes_id',
        'nama',
        'deskripsi',
    ];

    public function subtes(): BelongsTo
    {
        return $this->belongsTo(KategoriSubtes::class, 'subtes_id');
    }

    public function variasi(): HasMany
    {
        return $this->hasMany(KategoriVariasi::class, 'materi_id');
    }
}
