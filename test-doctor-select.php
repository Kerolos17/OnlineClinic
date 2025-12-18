<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Doctor Select Options ===\n\n";

// Get all doctors
$doctors = App\Models\Doctor::with('user')->get();

echo "Total doctors: " . $doctors->count() . "\n\n";

if ($doctors->isEmpty()) {
    echo "⚠️  No doctors found!\n";
    exit;
}

echo "Doctors list:\n";
foreach ($doctors as $doctor) {
    $label = $doctor->user->name_en ?? $doctor->user->email;
    echo "- ID: {$doctor->id} | Label: {$label}\n";
}

echo "\n=== Testing Booking ===\n\n";

// Get a sample booking
$booking = App\Models\Booking::with(['doctor.user', 'slot'])->first();

if ($booking) {
    echo "Booking ID: {$booking->id}\n";
    echo "Doctor ID: {$booking->doctor_id}\n";
    echo "Doctor Name: " . ($booking->doctor->user->name_en ?? 'N/A') . "\n";
    echo "Slot: {$booking->slot->date->format('Y-m-d')} | {$booking->slot->start_time} - {$booking->slot->end_time}\n";
} else {
    echo "No bookings found\n";
}

echo "\n✅ Test completed!\n";
