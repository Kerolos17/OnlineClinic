<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ZoomService;

echo "Testing Zoom API Connection...\n\n";

try {
    $zoom = new ZoomService();
    
    // Test creating a meeting
    $meeting = $zoom->createMeeting(
        userId: 'me',
        topic: 'Test Meeting - ' . date('Y-m-d H:i:s'),
        startTime: now()->addHour()->toIso8601String(),
        duration: 30
    );
    
    echo "✅ SUCCESS! Meeting created:\n";
    echo "Meeting ID: " . ($meeting['id'] ?? 'N/A') . "\n";
    echo "Join URL: " . ($meeting['join_url'] ?? 'N/A') . "\n";
    echo "Start URL: " . ($meeting['start_url'] ?? 'N/A') . "\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nFull error:\n";
    echo $e->getTraceAsString();
}
