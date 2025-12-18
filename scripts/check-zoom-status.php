<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Zoom Meeting Status...\n\n";

$bookings = App\Models\Booking::with('doctor.user')->get();

echo "Total Bookings: " . $bookings->count() . "\n";
echo "Bookings with Zoom: " . $bookings->whereNotNull('zoom_meeting_id')->count() . "\n";
echo "Bookings without Zoom: " . $bookings->whereNull('zoom_meeting_id')->count() . "\n\n";

echo "Details:\n";
echo str_repeat("-", 80) . "\n";

foreach ($bookings as $booking) {
    $hasZoom = $booking->zoom_meeting_id ? '✅' : '❌';
    echo "{$hasZoom} Booking #{$booking->id} - {$booking->patient_name}\n";
    
    if ($booking->zoom_meeting_id) {
        echo "   Meeting ID: {$booking->zoom_meeting_id}\n";
        echo "   Join URL: {$booking->zoom_join_url}\n";
    }
    
    echo "\n";
}

echo str_repeat("-", 80) . "\n";
