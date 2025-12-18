<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $fillable = [
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function booking()
    {
        return $this->hasOne(Booking::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeBooked($query)
    {
        return $query->where('status', 'booked');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeFuture($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', now()->toDateString());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('date')->orderBy('start_time');
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeInTimeRange($query, $startTime, $endTime)
    {
        return $query->where('start_time', '>=', $startTime)
            ->where('end_time', '<=', $endTime);
    }

    public function scopeWithDoctor($query)
    {
        return $query->with('doctor.user', 'doctor.specialization');
    }

    // Helper method to check if slot is bookable
    public function isBookable()
    {
        return $this->status === 'available' &&
               $this->date->isFuture();
    }

    // Helper method to mark as booked
    public function markAsBooked()
    {
        $this->update(['status' => 'booked']);
    }

    // Helper method to mark as available
    public function markAsAvailable()
    {
        $this->update(['status' => 'available']);
    }
}
