<?php

/**
 * Email Testing Script
 * Run with: php artisan tinker < test-emails.php
 */

echo "🧪 Testing WellClinic Email System\n";
echo "==================================\n\n";

// Get a booking with all relations
$booking = App\Models\Booking::with(['doctor.user', 'doctor.specialization'])->first();

if (!$booking) {
    echo "❌ No bookings found in database. Please create a booking first.\n";
    exit;
}

echo "✅ Found booking #" . $booking->id . "\n";
echo "   Patient: " . $booking->patient_name . "\n";
echo "   Email: " . $booking->patient_email . "\n";
echo "   Doctor: " . $booking->doctor->user->name_en . "\n\n";

// Test email address (change this to your email)
$testEmail = 'your-test-email@example.com';

echo "📧 Sending test emails to: " . $testEmail . "\n\n";

// Test 1: Booking Confirmation Email
echo "1️⃣ Testing BookingConfirmationMail...\n";
try {
    Mail::to($testEmail)->send(new App\Mail\BookingConfirmationMail($booking));
    echo "   ✅ Sent successfully!\n\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 2: Zoom Link Email (if zoom link exists)
if ($booking->zoom_join_url) {
    echo "2️⃣ Testing ZoomLinkMail...\n";
    try {
        Mail::to($testEmail)->send(new App\Mail\ZoomLinkMail($booking));
        echo "   ✅ Sent successfully!\n\n";
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "2️⃣ Skipping ZoomLinkMail (no zoom link in booking)\n\n";
}

// Test 3: Booking Reminder Email (if zoom link exists)
if ($booking->zoom_join_url) {
    echo "3️⃣ Testing BookingReminderMail...\n";
    try {
        Mail::to($testEmail)->send(new App\Mail\BookingReminderMail($booking));
        echo "   ✅ Sent successfully!\n\n";
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "3️⃣ Skipping BookingReminderMail (no zoom link in booking)\n\n";
}

echo "==================================\n";
echo "✅ Email testing complete!\n";
echo "Check your inbox at: " . $testEmail . "\n";
