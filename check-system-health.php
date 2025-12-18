<?php
/**
 * System Health Check
 * 
 * Quick script to check if everything is configured correctly
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== WellClinic System Health Check ===\n\n";

$issues = [];
$warnings = [];

// Check database connection
try {
    DB::connection()->getPdo();
    echo "✓ Database connection: OK\n";
} catch (\Exception $e) {
    echo "✗ Database connection: FAILED\n";
    $issues[] = "Database connection failed: " . $e->getMessage();
}

// Check queue configuration
$queueConnection = config('queue.default');
echo "✓ Queue connection: {$queueConnection}\n";

// Check jobs in queue
$jobsCount = DB::table('jobs')->count();
echo "  Jobs in queue: {$jobsCount}\n";

if ($jobsCount > 10) {
    $warnings[] = "High number of jobs in queue ({$jobsCount}). Queue worker might not be running.";
}

// Check failed jobs
$failedJobsCount = DB::table('failed_jobs')->count();
echo "  Failed jobs: {$failedJobsCount}\n";

if ($failedJobsCount > 0) {
    $warnings[] = "There are {$failedJobsCount} failed jobs. Check with: php artisan queue:failed";
}

// Check Zoom configuration
$zoomConfigured = config('services.zoom.account_id') && 
                  config('services.zoom.client_id') && 
                  config('services.zoom.client_secret');

if ($zoomConfigured) {
    echo "✓ Zoom API: Configured\n";
} else {
    echo "✗ Zoom API: NOT configured\n";
    $issues[] = "Zoom API credentials missing in .env file";
}

// Check mail configuration
$mailConfigured = config('mail.mailers.smtp.host') && 
                  config('mail.mailers.smtp.username');

if ($mailConfigured) {
    echo "✓ Mail: Configured\n";
} else {
    echo "✗ Mail: NOT configured\n";
    $issues[] = "Mail credentials missing in .env file";
}

// Check bookings without Zoom
$bookingsWithoutZoom = \App\Models\Booking::whereNull('zoom_meeting_id')
    ->where('status', 'confirmed')
    ->where('appointment_at', '>', now())
    ->count();

echo "\n--- Bookings Status ---\n";
echo "Future bookings without Zoom: {$bookingsWithoutZoom}\n";

if ($bookingsWithoutZoom > 0) {
    $warnings[] = "{$bookingsWithoutZoom} future booking(s) need Zoom meetings. Run: php process-pending-zoom.php";
}

// Summary
echo "\n=== Summary ===\n";

if (empty($issues) && empty($warnings)) {
    echo "✓ All systems operational!\n";
} else {
    if (!empty($issues)) {
        echo "\n⚠️ ISSUES:\n";
        foreach ($issues as $issue) {
            echo "  - {$issue}\n";
        }
    }
    
    if (!empty($warnings)) {
        echo "\n⚠️ WARNINGS:\n";
        foreach ($warnings as $warning) {
            echo "  - {$warning}\n";
        }
    }
}

echo "\n=== Recommendations ===\n";
echo "1. Make sure queue worker is running: composer dev\n";
echo "2. Check failed jobs regularly: php artisan queue:failed\n";
echo "3. Process pending Zoom meetings: php process-pending-zoom.php\n";
