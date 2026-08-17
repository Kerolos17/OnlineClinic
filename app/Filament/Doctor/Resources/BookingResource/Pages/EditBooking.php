<?php
namespace App\Filament\Doctor\Resources\BookingResource\Pages;

use App\Filament\Doctor\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('doctor.edit_booking');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Only allow updating status and doctor_notes
        return [
            'status'       => $data['status'],
            'doctor_notes' => $data['doctor_notes'] ?? null,
        ];
    }
}
