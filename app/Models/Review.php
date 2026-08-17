<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'doctor_id',
        'booking_id',
        'patient_name',
        'patient_email',
        'rating',
        'comment',
        'status',
        'created_at',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /** @return BelongsTo<Doctor, $this> */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /** @return BelongsTo<Booking, $this> */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
