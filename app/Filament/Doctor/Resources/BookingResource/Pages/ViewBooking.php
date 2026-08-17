<?php

namespace App\Filament\Doctor\Resources\BookingResource\Pages;

use App\Filament\Doctor\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

/**
 * @property-read \App\Models\Booking $record
 */
class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => in_array($this->record->status, ['pending', 'confirmed'])),

            Actions\Action::make('zoom_meeting')
                ->label(__('doctor.join_zoom'))
                ->icon('heroicon-o-video-camera')
                ->color('info')
                ->url(fn () => $this->record->zoom_start_url)
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->slot?->isOnline() && ! empty($this->record->zoom_start_url)),

            Actions\Action::make('confirm_booking')
                ->label(__('doctor.confirm'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->record->update(['status' => 'confirmed']);
                    $this->refreshFormData(['status']);
                })
                ->requiresConfirmation()
                ->modalHeading(__('doctor.confirm_booking'))
                ->modalDescription(__('doctor.confirm_booking_description'))
                ->visible(fn () => $this->record->status === 'pending'),

            Actions\Action::make('complete_booking')
                ->label(__('doctor.complete'))
                ->icon('heroicon-o-check-badge')
                ->color('info')
                ->action(function () {
                    $this->record->update(['status' => 'completed']);
                    $this->refreshFormData(['status']);
                })
                ->requiresConfirmation()
                ->modalHeading(__('doctor.complete_booking'))
                ->modalDescription(__('doctor.complete_booking_description'))
                ->visible(fn () => $this->record->status === 'confirmed' && $this->record->appointment_at < now()),
        ];
    }

    public function getTitle(): string
    {
        return __('doctor.booking_details');
    }
}
