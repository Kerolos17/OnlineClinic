<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Processing Pending Zoom Meetings ===\n\n";

// Get bookings that need Zoom meetings (future appointments only)
$bookings = \App\Models\Booking::with(['doctor.user', 'doctor.specialization'])
    ->whereNull('zoom_meeting_id')
    ->where('status', 'confirmed')
    ->where('appointment_at', '>', now())
    ->get();

if ($bookings->isEmpty()) {
    echo "No pending bookings found.\n";
    exit;
}

echo "Found {$bookings->count()} booking(s) to process:\n\n";

foreach ($bookings as $booking) {
    echo "Booking #{$booking->id}\n";
    echo "  Patient: {$booking->patient_name}\n";
    echo "  Appointment: {$booking->appointment_at->format('Y-m-d H:i')}\n";

    try {
        $zoom = app(\App\Services\ZoomService::class);

        $meeting = $zoom->createMeeting(
            userId: 'me',
            topic: 'Consultation with Dr. '.($booking->doctor->name['en'] ?? 'Doctor').' - '.$booking->patient_name,
            startTime: $booking->appointment_at->toIso8601String(),
            duration: 45
        );

        $booking->update([
            'zoom_meeting_id' => $meeting['id'],
            'zoom_join_url' => $meeting['join_url'],
            'zoom_start_url' => $meeting['start_url'],
            'zoom_created_at' => now(),
        ]);

        echo "  ✓ Zoom meeting created (ID: {$meeting['id']})\n";

        // Send emails
        \Illuminate\Support\Facade($booking->patnt_email)
            ->send(new \App\Mail\BookingReminderMail($booking, 'patient'));
        echo "  ✓ Email sent to patient ({$booking->patient_email})\n";

        \Illuminate\Support\Facades\Mail::to($booking->doctor->user->email)
            ->send(new \App\Mail\BookingReminderMail($booking, 'doctor'));
        echo "  ✓ Email sent to doctor ({$booking->doctor->user->email})\n";

    } catch (\Exception $e) {
        echo "  ✗ Error: {$e->getMessage()}\n";
    }

    echo "\n";
}

echo "Done!\n";
