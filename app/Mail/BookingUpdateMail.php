<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public $type;

    public $changes;

    /**
     * Create a new message instance.
     *
     * @param  array|null  $changes  List of changed fields as ['field' => old value => new value]
     */
    public function __construct($booking, string $type, ?array $changes = null)
    {
        $this->booking = $booking;
        $this->type = $type; // 'cancellation' | 'modification' | 'zoom_update'
        $this->changes = $changes;
    }

    public function build()
    {
        $doctorName = $this->booking->doctor->user->getDisplayName();

        $subject = match ($this->type) {
            'cancellation' => 'Appointment Cancelled - WellClinic',
            'modification' => 'Appointment Updated - WellClinic',
            'zoom_update' => 'Zoom Meeting Link Updated - WellClinic',
            default => 'Appointment Update - WellClinic',
        };

        return $this->subject($subject)
            ->view('emails.booking_update')
            ->with([
                'patientName' => $this->booking->patient_name,
                'doctorName' => $doctorName,
                'appointmentTime' => $this->booking->appointment_at
                    ? $this->booking->appointment_at->format('l, F j, Y \a\t g:i A')
                    : 'N/A',
                'type' => $this->type,
                'changes' => $this->changes,
                'zoomJoinUrl' => $this->booking->zoom_join_url,
            ]);
    }
}
