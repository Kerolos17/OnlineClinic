<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
