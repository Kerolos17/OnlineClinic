<?php
namespace App\Filament\Doctor\Widgets;

use App\Models\Slot;
use App\Services\ZoomService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ZoomStatusWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $doctor = Auth::user()->doctor;

        if (! $doctor) {
            return [];
        }

        // Get upcoming online appointments
        $upcomingOnlineSlots = Slot::where('doctor_id', $doctor->id)
            ->where('type', 'online')
            ->where('date', '>=', now()->toDateString())
            ->where('status', 'booked')
            ->with('booking')
            ->get();

        $totalOnlineAppointments = $upcomingOnlineSlots->count();
        $meetingsReady           = $upcomingOnlineSlots->filter(fn($slot) => $slot->hasZoomMeeting())->count();
        $meetingsMissing         = $totalOnlineAppointments - $meetingsReady;

        // Check Zoom API status
        $zoomApiStatus = $this->checkZoomApiStatus();

        return [
            Stat::make('Online Appointments', $totalOnlineAppointments)
                ->description('Upcoming online consultations')
                ->descriptionIcon('heroicon-o-video-camera')
                ->color('primary'),

            Stat::make('Zoom Meetings Ready', $meetingsReady)
                ->description('Meetings with valid Zoom links')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($meetingsMissing > 0 ? 'warning' : 'success'),

            Stat::make('Meetings Missing', $meetingsMissing)
                ->description('Appointments without Zoom meetings')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($meetingsMissing > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'class' => $meetingsMissing > 0 ? 'cursor-pointer' : '',
                ])
                ->url($meetingsMissing > 0 ? route('filament.doctor.resources.appointments.index') : null),

            Stat::make('Zoom API Status', $zoomApiStatus['status'])
                ->description($zoomApiStatus['message'])
                ->descriptionIcon($zoomApiStatus['icon'])
                ->color($zoomApiStatus['color']),
        ];
    }

    protected function checkZoomApiStatus(): array
    {
        $cacheKey = 'zoom_api_status_check';

        return Cache::remember($cacheKey, 300, function () { // Cache for 5 minutes
            try {
                $zoomService = app(ZoomService::class);

                // Try to get access token to test API connectivity
                $reflection = new \ReflectionClass($zoomService);
                $method     = $reflection->getMethod('getAccessToken');
                $method->setAccessible(true);
                $token = $method->invoke($zoomService);

                if ($token) {
                    return [
                        'status'  => 'Connected',
                        'message' => 'Zoom API is operational',
                        'icon'    => 'heroicon-o-check-circle',
                        'color'   => 'success',
                    ];
                }

            } catch (\Exception $e) {
                \Log::warning('Zoom API status check failed', [
                    'error' => $e->getMessage(),
                ]);

                return [
                    'status'  => 'Error',
                    'message' => 'Zoom API connection failed',
                    'icon'    => 'heroicon-o-x-circle',
                    'color'   => 'danger',
                ];
            }

            return [
                'status'  => 'Unknown',
                'message' => 'Unable to check Zoom API status',
                'icon'    => 'heroicon-o-question-mark-circle',
                'color'   => 'warning',
            ];
        });
    }

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->doctor !== null;
    }
}
