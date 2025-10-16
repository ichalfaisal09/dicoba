<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriSubtes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kategori_subtes';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
    ];

    public function materi(): HasMany
    {
        return $this->hasMany(KategoriMateri::class, 'subtes_id');
    }

    public function variasi(): HasManyThrough
    {
        return $this->hasManyThrough(
            KategoriVariasi::class,
            KategoriMateri::class,
            'subtes_id',
            'materi_id'
        );
    }
}
