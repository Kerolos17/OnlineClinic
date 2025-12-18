<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
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
        // Get specialization name
        $spec = $this->booking->doctor->specialization;
        $specializationName = app()->getLocale() == 'ar'
            ? $spec->name_ar
            : $spec->name_en;

        // Use the User model's getDisplayName() method which handles locale automatically
        $doctorName = $this->booking->doctor->user->getDisplayName();

        // Format appointment date and time
        $appointmentTime = $this->booking->appointment_at
            ? $this->booking->appointment_at->format('l, F j, Y \a\t g:i A')
            : 'N/A';

        return $this->subject('Booking Confirmation - WellClinic')
            ->view('emails.booking_confirmation')
            ->with([
                'patientName' => $this->booking->patient_name,
                'doctorName' => $doctorName,
                'specialization' => $specializationName,
                'appointmentTime' => $appointmentTime,
                'amount' => $this->booking->amount,
                'bookingId' => $this->booking->id,
                'recipient' => $this->recipient,
            ]);
    }
}
