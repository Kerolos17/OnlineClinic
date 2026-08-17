<?php
namespace App\Filament\Doctor\Pages;

use App\Filament\Doctor\Widgets\DoctorStatsWidget;
use App\Filament\Doctor\Widgets\UpcomingAppointmentsWidget;
use App\Filament\Doctor\Widgets\ZoomStatusWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.doctor.pages.dashboard';

    public function getTitle(): string
    {
        return __('doctor.dashboard');
    }

    // ✅ FIX 1 & 2: إخفاء الـ default heading وبناء greeting منفصل
    public function getHeading(): string
    {
        return ''; // نخلي Filament يرندر حاجة فاضية فوق
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    // ✅ method جديدة للـ Hero Banner بس
    public function getGreeting(): string
    {
        $doctor = Auth::user()->doctor;
        $hour   = now()->hour;

        if ($hour < 12) {
            $greeting = __('doctor.good_morning');
        } elseif ($hour < 18) {
            $greeting = __('doctor.good_afternoon');
        } else {
            $greeting = __('doctor.good_evening');
        }

        // ✅ FIX 1: شيل 'Dr. ' من هنا لأن getDisplayName() بترجعه
        return $greeting . ', ' . $doctor->user->getDisplayName();
    }

    public function getWelcomeMessage(): string
    {
        return __('doctor.welcome_message');
    }

    public function getWidgets(): array
    {
        return [
            DoctorStatsWidget::class,
            ZoomStatusWidget::class,
            \App\Filament\Doctor\Widgets\CalendarWidget::class,
            UpcomingAppointmentsWidget::class,
        ];
    }
}
