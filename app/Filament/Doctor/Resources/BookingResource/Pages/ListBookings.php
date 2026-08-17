<?php
namespace App\Filament\Doctor\Resources\BookingResource\Pages;

use App\Filament\Doctor\Resources\BookingResource;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action since bookings are created by patients
        ];
    }

    public function getTitle(): string
    {
        return __('doctor.bookings');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // TODO: Add booking statistics widgets
        ];
    }
}
