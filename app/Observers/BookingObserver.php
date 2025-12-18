<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingStatusChangedNotification;
use App\Notifications\NewBookingNotification;
use Filament\Notifications\Notification as FilamentNotification;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        // Send notification to all admins when a new booking is created
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            // Traditional database notification
            $admin->notify(new NewBookingNotification($booking));

            // Filament real-time notification with polling
            FilamentNotification::make()
                ->title('New Booking')
                ->body("New booking from **{$booking->patient_name}** with Dr. **{$booking->doctor->user->name_en}**")
                ->icon('heroicon-o-calendar')
                ->iconColor('success')
                ->actions([
                    \Filament\Notifications\Actions\Action::make('view')
                        ->label('View')
                        ->url(route('filament.admin.resources.bookings.edit', $booking->id))
                        ->button()
                        ->markAsRead(),
                ])
                ->sendToDatabase($admin, isEventDispatched: true);
        }
    }

    /**
     * Handle the Booking "updating" event.
     */
    public function updating(Booking $booking): void
    {
        // Check if status has changed
        if ($booking->isDirty('status')) {
            $oldStatus = $booking->getOriginal('status');
            $newStatus = $booking->status;

            $statusLabels = [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ];

            $statusColors = [
                'pending' => 'warning',
                'confirmed' => 'success',
                'completed' => 'info',
                'cancelled' => 'danger',
            ];

            // Send notification to all admins when booking status changes
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                // Traditional database notification
                $admin->notify(new BookingStatusChangedNotification($booking, $oldStatus, $newStatus));

                // Filament real-time notification with polling
                FilamentNotification::make()
                    ->title('Booking Status Changed')
                    ->body("Booking #{$booking->id} changed from **{$statusLabels[$oldStatus]}** to **{$statusLabels[$newStatus]}**")
                    ->icon('heroicon-o-arrow-path')
                    ->iconColor($statusColors[$newStatus])
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->label('View')
                            ->url(route('filament.admin.resources.bookings.edit', $booking->id))
                            ->button()
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin, isEventDispatched: true);
            }
        }
    }
}
