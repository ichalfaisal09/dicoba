<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TryoutSession extends Model
{
    use HasFactory;

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_SUBMITTED = 'submitted';

    protected $fillable = [
        'tryout_booking_id',
        'status',
        'mulai_pada',
        'selesai_pada',
        'durasi_terpakai',
        'metadata',
    ];

    protected $casts = [
        'mulai_pada' => 'datetime',
        'selesai_pada' => 'datetime',
        'durasi_terpakai' => 'integer',
        'metadata' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(TryoutBooking::class, 'tryout_booking_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TryoutSessionAnswer::class, 'tryout_session_id');
    }
}
