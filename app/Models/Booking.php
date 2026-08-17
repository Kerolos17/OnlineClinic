<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_code',
        'doctor_id',
        'slot_id',
        'patient_name',
        'patient_email',
        'patient_phone',
        'patient_notes',
        'doctor_notes',
        'status',
        'amount',
        'zoom_meeting_id',
        'zoom_join_url',
        'zoom_start_url',
        'zoom_created_at',
        'appointment_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'zoom_created_at' => 'datetime',
        'appointment_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->reference_code)) {
                $booking->reference_code = strtoupper(Str::random(10));
            }
        });
    }

    /** @return BelongsTo<Doctor, $this> */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /** @return BelongsTo<Slot, $this> */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    /** @return HasOne<Payment, $this> */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function getIsUpcomingAttribute()
    {
        return $this->appointment_at && $this->appointment_at->isFuture();
    }
}
