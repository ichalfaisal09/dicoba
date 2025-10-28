<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class TryoutBooking extends Model
{
    use HasFactory;

    protected $table = 'tryout_booking';

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'tryout_paket_id',
        'status',
        // 'tanggal_mulai',
        // 'tanggal_selesai',
        // 'durasi_menit',
        'harga',
        'kode_pembayaran',
        'metadata',
    ];

    protected $casts = [
        // 'tanggal_mulai' => 'datetime',
        // 'tanggal_selesai' => 'datetime',
        // 'durasi_menit' => 'integer',
        'harga' => 'integer',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tryoutPaket(): BelongsTo
    {
        return $this->belongsTo(TryoutPaket::class, 'tryout_paket_id');
    }

    public function session(): HasOne
    {
        return $this->hasOne(TryoutSession::class, 'tryout_booking_id');
    }

    public function sessionAnswers(): HasManyThrough
    {
        return $this->hasManyThrough(
            TryoutSessionAnswer::class,
            TryoutSession::class,
            'tryout_booking_id',
            'tryout_session_id'
        );
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }
}
