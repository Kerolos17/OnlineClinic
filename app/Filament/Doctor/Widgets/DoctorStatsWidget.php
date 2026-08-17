<?php
namespace App\Filament\Doctor\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DoctorStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $doctor = Auth::user()->doctor;

        if (! $doctor) {
            return [];
        }

        // Today's appointments
        $todayAppointments = $doctor->slots()
            ->today()
            ->booked()
            ->count();

        // Weekly appointments (current week)
        $weekStart          = now()->startOfWeek();
        $weekEnd            = now()->endOfWeek();
        $weeklyAppointments = $doctor->slots()
            ->betweenDates($weekStart->toDateString(), $weekEnd->toDateString())
            ->booked()
            ->count();

        // Monthly appointments (current month)
        $monthStart          = now()->startOfMonth();
        $monthEnd            = now()->endOfMonth();
        $monthlyAppointments = $doctor->slots()
            ->betweenDates($monthStart->toDateString(), $monthEnd->toDateString())
            ->booked()
            ->count();

        // Total bookings
        $totalBookings = $doctor->bookings()->count();

        // Monthly revenue (completed bookings)
        $monthlyRevenue = $doctor->bookings()
            ->whereMonth('appointment_at', now()->month)
            ->whereYear('appointment_at', now()->year)
            ->where('status', 'completed')
            ->sum('amount');

        // Unique patients count
        $uniquePatients = $doctor->bookings()
            ->distinct('patient_email')
            ->count('patient_email');

        return [
            Stat::make(__('doctor.stats.today_appointments'), $todayAppointments)
                ->description(__('doctor.stats.appointments_today'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make(__('doctor.stats.weekly_appointments'), $weeklyAppointments)
                ->description(__('doctor.stats.appointments_this_week'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make(__('doctor.stats.monthly_appointments'), $monthlyAppointments)
                ->description(__('doctor.stats.appointments_this_month'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),

            Stat::make(__('doctor.stats.total_bookings'), $totalBookings)
                ->description(__('doctor.stats.all_time_bookings'))
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),

            Stat::make(__('doctor.stats.monthly_revenue'), '$' . number_format($monthlyRevenue, 2))
                ->description(__('doctor.stats.revenue_this_month'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make(__('doctor.stats.unique_patients'), $uniquePatients)
                ->description(__('doctor.stats.total_patients'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}
