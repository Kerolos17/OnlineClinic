<?php

namespace App\Console\Commands;

use App\Mail\BookingConfirmationMail;
use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmails extends Command
{
    protected $signature = 'email:test {email? : Email address to send test emails to}';

    protected $description = 'Test WellClinic email templates by sending sample emails';

    public function handle()
    {
        $this->info('🧪 Testing WellClinic Email System');
        $this->info('==================================');
        $this->newLine();

        $testEmail = $this->argument('email') ?? $this->ask('Enter email address to send test emails to');

        if (! filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Invalid email address!');

            return 1;
        }

        $booking = Booking::with(['doctor.user', 'doctor.specialization'])->first();

        if (! $booking) {
            $this->error('❌ No bookings found in database.');
            $this->info('💡 Create a booking first or run: php artisan db:seed');

            return 1;
        }

        $this->info("✅ Found booking #{$booking->id}");
        $this->line("   Patient: {$booking->patient_name}");
        $this->line("   Doctor: {$booking->doctor->user->name_en}");
        $this->newLine();

        $this->info("📧 Sending test emails to: {$testEmail}");
        $this->newLine();

        // Test 1: Booking Confirmation Email (Patient)
        $this->info('1️⃣ Sending BookingConfirmationMail to Patient...');
        try {
            Mail::to($testEmail)->send(new BookingConfirmationMail($booking, 'patient'));
            $this->line('   ✅ Sent successfully!');
        } catch (\Exception $e) {
            $this->error('   ❌ Error: '.$e->getMessage());
        }
        $this->newLine();

        // Test 2: Booking Confirmation Email (Doctor)
        $this->info('2️⃣ Sending BookingConfirmationMail to Doctor...');
        try {
            Mail::to($testEmail)->send(new BookingConfirmationMail($booking, 'doctor'));
            $this->line('   ✅ Sent successfully!');
        } catch (\Exception $e) {
            $this->error('   ❌ Error: '.$e->getMessage());
        }
        $this->newLine();

        // Test 3: Booking Reminder Email (Patient)
        if ($booking->zoom_join_url) {
            $this->info('3️⃣ Sending BookingReminderMail to Patient...');
            try {
                Mail::to($testEmail)->send(new BookingReminderMail($booking, 'patient'));
                $this->line('   ✅ Sent successfully!');
            } catch (\Exception $e) {
                $this->error('   ❌ Error: '.$e->getMessage());
            }
            $this->newLine();
        } else {
            $this->warn('3️⃣ Skipping BookingReminderMail (no zoom link in booking)');
            $this->line('   💡 Run CreateZoomMeeting job first or add zoom_join_url manually');
            $this->newLine();
        }

        // Test 4: Booking Reminder Email (Doctor)
        if ($booking->zoom_join_url) {
            $this->info('4️⃣ Sending BookingReminderMail to Doctor...');
            try {
                Mail::to($testEmail)->send(new BookingReminderMail($booking, 'doctor'));
                $this->line('   ✅ Sent successfully!');
            } catch (\Exception $e) {
                $this->error('   ❌ Error: '.$e->getMessage());
            }
            $this->newLine();
        } else {
            $this->warn('4️⃣ Skipping BookingReminderMail for Doctor (no zoom link in booking)');
            $this->newLine();
        }

        $this->newLine();
        $this->info('==================================');
        $this->info('✅ Email testing complete!');
        $this->line("📬 Check your inbox at: {$testEmail}");
        $this->newLine();

        return 0;
    }
}
