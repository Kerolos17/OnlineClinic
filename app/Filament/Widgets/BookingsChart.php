<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BookingsChart extends ChartWidget
{
    protected static ?string $heading = 'Bookings Overview (Last 7 Days)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $bookings = \App\Models\Booking::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => $bookings->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $bookings->pluck('date')
                ->map(fn ($date) => \Carbon\Carbon::parse($date)->format('M d'))
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    // Full width span
    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
