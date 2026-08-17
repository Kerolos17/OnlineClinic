<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'type',
        'notes',
        'zoom_meeting_id',
        'zoom_join_url',
        'zoom_start_url',
    ];

    protected $casts = [
        'date' => 'date',
        'type' => 'string',
    ];

    /** @return BelongsTo<Doctor, $this> */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /** @return HasOne<Booking, $this> */
    public function booking(): HasOne
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

    // Appointment type scopes
    public function scopeOnline($query)
    {
        return $query->where('type', 'online');
    }

    public function scopeClinic($query)
    {
        return $query->where('type', 'clinic');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Zoom meeting scopes
    public function scopeWithZoomMeeting($query)
    {
        return $query->whereNotNull('zoom_meeting_id');
    }

    public function scopeWithoutZoomMeeting($query)
    {
        return $query->whereNull('zoom_meeting_id');
    }

    // Helper methods for appointment types
    public function isOnline(): bool
    {
        return $this->type === 'online';
    }

    public function isClinic(): bool
    {
        return $this->type === 'clinic';
    }

    public function hasZoomMeeting(): bool
    {
        return ! empty($this->zoom_meeting_id);
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
