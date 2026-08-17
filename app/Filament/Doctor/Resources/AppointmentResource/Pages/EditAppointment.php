<?php

namespace App\Filament\Doctor\Resources\AppointmentResource\Pages;

use App\Filament\Doctor\Resources\AppointmentResource;
use App\Services\DoctorSlotService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @property-read \App\Models\Slot $record
 */
class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    public function getTitle(): string
    {
        return __('doctor.edit_appointment');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->status === 'available')
                ->requiresConfirmation()
                ->modalHeading(__('doctor.delete_appointment'))
                ->modalDescription(function () {
                    if ($this->record->booking) {
                        // Check 24-hour protection
                        if ($this->record->date->isToday() || $this->record->date->isTomorrow()) {
                            return __('doctor.delete_booked_appointment_24h_warning');
                        }

                        return __('doctor.delete_booked_appointment_warning');
                    }

                    return __('doctor.delete_appointment_confirmation');
                })
                ->modalSubmitActionLabel(__('doctor.delete'))
                ->before(function () {
                    // Check 24-hour protection for booked appointments
                    if ($this->record->booking &&
                        ($this->record->date->isToday() || $this->record->date->isTomorrow())) {
                        throw ValidationException::withMessages([
                            'appointment' => __('doctor.cannot_delete_appointment_24h_protection'),
                        ]);
                    }
                })
                ->action(function () {
                    $doctorSlotService = app(DoctorSlotService::class);

                    try {
                        // If appointment has booking, notify patient
                        if ($this->record->booking) {
                            // TODO: Implement patient notification system
                            // This would typically send email/SMS to patient about cancellation
                        }

                        $doctorSlotService->deleteSlot($this->record);

                        $this->redirect($this->getResource()::getUrl('index'));

                    } catch (\Exception $e) {
                        throw ValidationException::withMessages([
                            'appointment' => __('doctor.appointment_deletion_failed', ['error' => $e->getMessage()]),
                        ]);
                    }
                }),
        ];
    }

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        // Ensure the appointment belongs to the current doctor
        if ($this->record->doctor_id !== Auth::user()->doctor->id) {
            abort(403);
        }

        // Prevent editing booked appointments
        if ($this->record->status !== 'available') {
            abort(403, __('doctor.cannot_edit_booked_appointment'));
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Remove bulk creation fields for editing
        unset($data['create_bulk'], $data['end_date'], $data['recurring_days']);

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->before(function () {
                    // Show warning if appointment has existing booking
                    if ($this->record->booking) {
                        $this->addError('booking_warning', __('doctor.editing_booked_appointment_warning'));
                    }
                }),
            $this->getCancelFormAction(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $doctorSlotService = app(DoctorSlotService::class);

        try {
            // Remove bulk creation fields that shouldn't be used in updates
            unset($data['create_bulk'], $data['end_date'], $data['recurring_days']);

            /** @var \App\Models\Slot $record */
            return $doctorSlotService->updateSlot($record, $data);
        } catch (ValidationException $e) {
            // Re-throw validation exceptions to show in the form
            throw $e;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw ValidationException::withMessages([
                'general' => __('doctor.appointment_update_failed', ['error' => $e->getMessage()]),
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('doctor.appointment_updated');
    }
}
