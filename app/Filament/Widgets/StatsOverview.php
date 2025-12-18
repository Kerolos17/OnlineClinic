<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Today\'s Bookings', \App\Models\Booking::whereDate('appointment_at', today())->count())
                ->description('Scheduled for today')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
                // ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
            Stat::make('Total Bookings', \App\Models\Booking::count())
                ->description('All time bookings')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
            Stat::make('Active Doctors', \App\Models\Doctor::where('is_active', true)->count())
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Pending Bookings', \App\Models\Booking::where('status', 'pending')->count())
                ->description('Awaiting confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
