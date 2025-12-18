<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateUpcomingZoomMeetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:create-upcoming';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Zoom meetings for upcoming appointments (within 1 hour)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oneHourFromNow = now()->addHour();

        $bookings = \App\Models\Booking::where('status', 'confirmed')
            ->whereNull('zoom_meeting_id')
            ->where('appointment_at', '<=', $oneHourFromNow)
            ->where('appointment_at', '>', now())
            ->get();

        foreach ($bookings as $booking) {
            \App\Jobs\CreateZoomMeeting::dispatch($booking);
            $this->info("Queued Zoom meeting creation for booking #{$booking->id}");
        }

        $this->info("Processed {$bookings->count()} bookings");
    }
}
