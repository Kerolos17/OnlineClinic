<?php
namespace App\Filament\Doctor\Resources\AppointmentResource\Pages;

use App\Filament\Doctor\Resources\AppointmentResource;
use App\Services\DoctorSlotService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    public function getTitle(): string
    {
        return __('doctor.create_appointment');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Add the current doctor's ID
        $data['doctor_id'] = Auth::user()->doctor->id;

        // Set default status
        $data['status'] = 'available';

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $doctorSlotService = app(DoctorSlotService::class);
        $doctor            = Auth::user()->doctor;

        try {
            // Check if this is bulk creation
            if ($data['create_bulk'] ?? false) {
                $bulkData = [
                    'type'           => $data['type'],
                    'start_date'     => $data['date'],
                    'end_date'       => $data['end_date'],
                    'start_time'     => $data['start_time'],
                    'end_time'       => $data['end_time'],
                    'recurring_days' => $data['recurring_days'] ?? [],
                    'notes'          => $data['notes'] ?? null,
                ];

                $slots = $doctorSlotService->createBulkSlots($doctor, $bulkData);

                // Return the first slot for the redirect
                return $slots->first();
            } else {
                // Single slot creation
                return $doctorSlotService->createSlot($doctor, $data);
            }
        } catch (ValidationException $e) {
            // Re-throw validation exceptions to show in the form
            throw $e;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw ValidationException::withMessages([
                'general' => __('doctor.appointment_creation_failed', ['error' => $e->getMessage()]),
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        $data = $this->form->getState();

        if ($data['create_bulk'] ?? false) {
            return __('doctor.bulk_appointments_created');
        }

        return __('doctor.appointment_created');
    }
}
