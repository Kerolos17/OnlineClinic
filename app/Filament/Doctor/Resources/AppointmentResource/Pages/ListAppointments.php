<?php
namespace App\Filament\Doctor\Resources\AppointmentResource\Pages;

use App\Filament\Doctor\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('doctor.create_appointment'))
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTitle(): string
    {
        return __('doctor.appointments');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // We'll add appointment statistics widgets here later
        ];
    }
}
