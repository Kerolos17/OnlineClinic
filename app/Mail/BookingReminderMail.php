<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public $recipient;

    public function __construct($booking, $recipient = 'patient')
    {
        $this->booking = $booking;
        $this->recipient = $recipient; // 'patient' or 'doctor'
    }

    public function build()
    {
        // Determine which Zoom link to use
        $zoomLink = $this->recipient === 'doctor'
            ? ($this->booking->zoom_start_url ?? $this->booking->zoom_join_url)
            : $this->booking->zoom_join_url;

        // Use the User model's getDisplayName() method which handles locale automatically
        $doctorName = $this->booking->doctor->user->getDisplayName();

        return $this->subject('Reminder: Upcoming Appointment - WellClinic')
            ->view('emails.booking_reminder')
            ->with([
                'patientName' => $this->booking->patient_name,
                'doctorName' => $doctorName,
                'appointmentTime' => $this->booking->appointment_at->format('l, F j, Y \a\t g:i A'),
                'timeUntil' => $this->booking->appointment_at->diffForHumans(),
                'zoomLink' => $zoomLink,
                'recipient' => $this->recipient,
            ]);
    }
}
