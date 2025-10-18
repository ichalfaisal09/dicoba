<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TryoutPaket extends Model
{
    use HasFactory;

    protected $table = 'tryout_paket';

    protected $fillable = [
        'nama',
        'waktu_pengerjaan',
        'harga',
        'is_aktif',
    ];

    protected $casts = [
        'waktu_pengerjaan' => 'integer',
        'harga' => 'integer',
    ];

    public function konfigurasiDasar(): BelongsToMany
    {
        return $this->belongsToMany(KonfigurasiDasarSistem::class, 'konfigurasi_ke_tryout', 'tryout_paket_id', 'konfigurasi_dasar_sistem_id')
            ->withPivot('urutan')
            ->withTimestamps()
            ->orderBy('konfigurasi_ke_tryout.urutan');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TryoutBooking::class, 'tryout_paket_id');
    }
}
