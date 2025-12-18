<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'patient_name' => $this->booking->patient_name,
            'doctor_name' => $this->booking->doctor->user->name_en,
            'appointment_at' => $this->booking->appointment_at->format('Y-m-d H:i'),
            'amount' => $this->booking->amount,
            'title' => 'New Booking',
            'message' => "New booking from {$this->booking->patient_name} with {$this->booking->doctor->user->name_en}",
        ];
    }
}
