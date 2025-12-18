<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'doctor_id',
        'slot_id',
        'patient_name',
        'patient_email',
        'patient_phone',
        'patient_notes',
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

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getIsUpcomingAttribute()
{
    return $this->appointment_at && $this->appointment_at->isFuture();
}

}
