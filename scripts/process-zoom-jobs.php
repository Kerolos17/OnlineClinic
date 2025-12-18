<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Processing pending Zoom meeting jobs...\n\n";

// Check for pending jobs
$pendingJobs = DB::table('jobs')->count();
echo "Pending jobs in queue: {$pendingJobs}\n";

if ($pendingJobs > 0) {
    echo "Processing jobs...\n";
    Artisan::call('queue:work', [
        '--once' => true,
        '--tries' => 1,
    ]);
    echo Artisan::output();
}

// Check for bookings without Zoom links
$bookingsWithoutZoom = DB::table('bookings')
    ->whereNull('zoom_meeting_id')
    ->where('status', 'confirmed')
    ->get();

echo "\nBookings without Zoom meetings: " . $bookingsWithoutZoom->count() . "\n";

if ($bookingsWithoutZoom->count() > 0) {
    echo "\nCreating Zoom meetings for existing bookings...\n";
    
    foreach ($bookingsWithoutZoom as $bookingData) {
        $booking = App\Models\Booking::find($bookingData->id);
        
        try {
            echo "Processing booking #{$booking->id}...\n";
            App\Jobs\CreateZoomMeeting::dispatch($booking);
            echo "  ✅ Job dispatched\n";
        } catch (\Exception $e) {
            echo "  ❌ Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\nNow processing the dispatched jobs...\n";
    Artisan::call('queue:work', [
        '--stop-when-empty' => true,
        '--tries' => 1,
    ]);
    echo Artisan::output();
}

echo "\n✅ Done!\n";
