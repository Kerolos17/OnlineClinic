<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=================================================\n";
echo "   Zoom Meeting Integration - Verification\n";
echo "=================================================\n\n";

// 1. Check Zoom Configuration
echo "1. Checking Zoom Configuration...\n";
$zoomConfigured = config('services.zoom.account_id') &&
                  config('services.zoom.client_id') &&
                  config('services.zoom.client_secret');

if ($zoomConfigured) {
    echo "   ✅ Zoom credentials configured\n";
    echo '   Account ID: '.substr(config('services.zoom.account_id'), 0, 10)."...\n";
} else {
    echo "   ❌ Zoom credentials missing\n";
}

// 2. Check Queue Configuration
echo "\n2. Checking Queue Configuration...\n";
echo '   Queue Driver: '.config('queue.default')."\n";

$pendingJobs = DB::table('jobs')->count();
$failedJobs = DB::table('failed_jobs')->count();

echo "   Pending Jobs: {$pendingJobs}\n";
echo "   Failed Jobs: {$failedJobs}\n";

// 3. Check Bookings
echo "\n3. Checking Bookings...\n";
$totalBookings = App\Models\Booking::count();
$withZoom = App\Models\Booking::whereNotNull('zoom_meeting_id')->count();
$withoutZoom = App\Models\Booking::whereNull('zoom_meeting_id')->count();

echo "   Total Bookings: {$totalBookings}\n";
echo "   With Zoom: {$withZoom} ✅\n";
echo "   Without Zoom: {$withoutZoom} ".($withoutZoom > 0 ? '⚠️' : '✅')."\n";

// 4. Show Recent Bookings
if ($totalBookings > 0) {
    echo "\n4. Recent Bookings:\n";
    echo '   '.str_repeat('-', 70)."\n";

    $recentBookings = App\Models\Booking::with('doctor.user')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    foreach ($recentBookings as $booking) {
        $status = $booking->zoom_meeting_id ? '✅' : '❌';
        echo "   {$status} #{$booking->id} - {$booking->patient_name}\n";

        if ($booking->zoom_meeting_id) {
            echo "      Meeting ID: {$booking->zoom_meeting_id}\n";
            echo '      Join URL: '.substr($booking->zoom_join_url, 0, 50)."...\n";
        }
    }

    echo '   '.str_repeat('-', 70)."\n";
}

// 5. Test Zoom API Connection
echo "\n5. Testing Zoom API Connection...\n";
try {
    $zoom = new App\Services\ZoomService;
    $testMeeting = $zoom->createMeeting(
        userId: 'me',
        topic: 'Test Connection - '.date('Y-m-d H:i:s'),
        startTime: now()->addHour()->toIso8601String(),
        duration: 30
    );

    echo "   ✅ Zoom API connection successful\n";
    echo '   Test Meeting ID: '.($testMeeting['id'] ?? 'N/A')."\n";

} catch (\Exception $e) {
    echo "   ❌ Zoom API connection failed\n";
    echo '   Error: '.$e->getMessage()."\n";
}

// Summary
echo "\n=================================================\n";
echo "   Summary\n";
echo "=================================================\n";

$allGood = $zoomConfigured && $withoutZoom == 0 && $failedJobs == 0;

if ($allGood) {
    echo "   ✅ Everything is working correctly!\n";
} else {
    echo "   ⚠️  Some issues need attention:\n";

    if (! $zoomConfigured) {
        echo "      - Configure Zoom credentials in .env\n";
    }

    if ($withoutZoom > 0) {
        echo "      - Run: php process-zoom-jobs.php\n";
    }

    if ($failedJobs > 0) {
        echo "      - Check failed jobs: php artisan queue:failed\n";
    }

    if ($pendingJobs > 0) {
        echo "      - Process pending jobs: php artisan queue:work\n";
    }
}

echo "\n";
