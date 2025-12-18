<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string $oldStatus,
        public string $newStatus
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'title' => 'Booking Status Changed',
            'message' => "Booking #{$this->booking->id} status changed from {$this->oldStatus} to {$this->newStatus}",
        ];
    }
}
