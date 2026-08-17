<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DoctorSlotService
{
    protected ZoomService $zoomService;

    protected PatientNotificationService $notificationService;

    public function __construct(ZoomService $zoomService, PatientNotificationService $notificationService)
    {
        $this->zoomService = $zoomService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create a single appointment slot with overlap validation
     */
    public function createSlot(Doctor $doctor, array $slotData): Slot
    {
        $this->validateSlotData($slotData);
        $this->validateNoOverlap($doctor, $slotData);

        return DB::transaction(function () use ($doctor, $slotData) {
            $slot = $doctor->slots()->create([
                'date' => $slotData['date'],
                'start_time' => $slotData['start_time'],
                'end_time' => $slotData['end_time'],
                'type' => $slotData['type'],
                'notes' => $slotData['notes'] ?? null,
                'status' => 'available',
            ]);

            if ($slot->isOnline()) {
                $this->createZoomMeetingForSlot($slot);
            }

            Log::info('Slot created successfully', [
                'doctor_id' => $doctor->id,
                'slot_id' => $slot->id,
                'type' => $slot->type,
                'has_zoom' => $slot->hasZoomMeeting(),
            ]);

            return $slot;
        });
    }

    /**
     * Create multiple slots with recurring patterns
     */
    public function createBulkSlots(Doctor $doctor, array $bulkData): Collection
    {
        $this->validateBulkSlotData($bulkData);

        $slots = collect();
        $errors = collect();

        DB::transaction(function () use ($doctor, $bulkData, &$slots, &$errors) {
            $dates = $this->generateRecurringDates($bulkData);

            foreach ($dates as $date) {
                foreach ($bulkData['time_slots'] as $timeSlot) {
                    try {
                        $slotData = [
                            'date' => $date,
                            'start_time' => $timeSlot['start_time'],
                            'end_time' => $timeSlot['end_time'],
                            'type' => $bulkData['type'],
                            'notes' => $bulkData['notes'] ?? null,
                        ];

                        if ($this->hasOverlap($doctor, $slotData)) {
                            $errors->push([
                                'date' => $date,
                                'time' => $timeSlot['start_time'].'-'.$timeSlot['end_time'],
                                'error' => 'Time slot overlaps with existing appointment',
                            ]);

                            continue;
                        }

                        $slot = $this->createSlot($doctor, $slotData);
                        $slots->push($slot);

                    } catch (\Exception $e) {
                        $errors->push([
                            'date' => $date,
                            'time' => $timeSlot['start_time'].'-'.$timeSlot['end_time'],
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        });

        if ($errors->isNotEmpty()) {
            Log::warning('Bulk slot creation had errors', [
                'doctor_id' => $doctor->id,
                'created_count' => $slots->count(),
                'error_count' => $errors->count(),
                'errors' => $errors->toArray(),
            ]);
        }

        return $slots;
    }

    /**
     * Update an existing slot
     */
    public function updateSlot(Slot $slot, array $updateData): Slot
    {
        $this->validateSlotData($updateData);
        $this->validateNoOverlap($slot->doctor, $updateData, $slot->id);

        return DB::transaction(function () use ($slot, $updateData) {
            $oldType = $slot->type;
            $oldDate = $slot->date->format('Y-m-d');
            $oldStartTime = $slot->start_time;
            $oldEndTime = $slot->end_time;

            $changes = [];
            if ($oldDate !== $updateData['date']) {
                $changes['date'] = ['from' => $oldDate, 'to' => $updateData['date']];
            }
            if ($oldStartTime !== $updateData['start_time'] || $oldEndTime !== $updateData['end_time']) {
                $changes['time'] = [
                    'from' => $oldStartTime.' - '.$oldEndTime,
                    'to' => $updateData['start_time'].' - '.$updateData['end_time'],
                ];
            }
            if ($oldType !== $updateData['type']) {
                $changes['type'] = ['from' => $oldType, 'to' => $updateData['type']];
            }

            $slot->update([
                'date' => $updateData['date'],
                'start_time' => $updateData['start_time'],
                'end_time' => $updateData['end_time'],
                'type' => $updateData['type'],
                'notes' => $updateData['notes'] ?? $slot->notes,
            ]);

            $zoomUpdated = false;
            if ($oldType !== $updateData['type']) {
                if ($updateData['type'] === 'online' && ! $slot->hasZoomMeeting()) {
                    $this->createZoomMeetingForSlot($slot);
                    $zoomUpdated = true;
                } elseif ($updateData['type'] === 'clinic' && $slot->hasZoomMeeting()) {
                    $this->deleteZoomMeetingForSlot($slot);
                    $zoomUpdated = true;
                }
            } elseif ($slot->isOnline() && $slot->hasZoomMeeting()) {
                if (! empty($changes['date']) || ! empty($changes['time'])) {
                    $this->updateZoomMeetingForSlot($slot);
                    $zoomUpdated = true;
                }
            }

            if (! empty($changes) && $slot->booking) {
                $this->notificationService->notifyAppointmentModification($slot, $changes);
                if ($zoomUpdated && $slot->isOnline()) {
                    $this->notificationService->notifyZoomMeetingUpdate($slot);
                }
            }

            Log::info('Slot updated', [
                'slot_id' => $slot->id,
                'doctor_id' => $slot->doctor_id,
                'changes' => $changes,
                'zoom_updated' => $zoomUpdated,
                'has_booking' => $slot->booking !== null,
            ]);

            return $slot->fresh();
        });
    }

    /**
     * Delete a slot with safety checks
     */
    public function deleteSlot(Slot $slot, bool $force = false): bool
    {
        if ($slot->booking && ! $force) {
            if ($slot->date->isToday() || $slot->date->isTomorrow()) {
                throw ValidationException::withMessages([
                    'slot' => __('doctor.cannot_delete_appointment_24h_protection'),
                ]);
            }
        }

return DB::transaction(function () use ($slot) {
            if ($slot->hasZoomMeeting()) {
                $this->deleteZoomMeetingForSlot($slot);
            }

            $deleted = Slot::destroy($slot->id);

            if ($slot->booking) {
                $this->notificationService->notifyAppointmentCancellation($slot);
            }

            Log::info('Slot deleted', [
                'slot_id'     => $slot->id,
                'doctor_id'   => $slot->doctor_id,
                'had_zoom'    => $slot->hasZoomMeeting(),
                'had_booking' => $slot->booking !== null,
            ]);

            return (bool) $deleted;
        });
    }

    /**
     * Validate slot data
     */
    protected function validateSlotData(array $data): void
    {
        $required = ['date', 'start_time', 'end_time', 'type'];

        foreach ($required as $field) {
            if (! isset($data[$field]) || empty($data[$field])) {
                throw ValidationException::withMessages([
                    $field => "The {$field} field is required.",
                ]);
            }
        }

        if (! in_array($data['type'], ['online', 'clinic'])) {
            throw ValidationException::withMessages([
                'type' => 'Appointment type must be either online or clinic.',
            ]);
        }

        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);

        if ($endTime->lte($startTime)) {
            throw ValidationException::withMessages([
                'end_time' => 'End time must be after start time.',
            ]);
        }

        // Validate date is not in the past (allow today)
        $date = Carbon::parse($data['date'])->startOfDay();
        if ($date->lt(Carbon::today())) {
            throw ValidationException::withMessages([
                'date' => 'Cannot create appointments in the past.',
            ]);
        }
    }

    /**
     * Validate bulk slot data
     */
    protected function validateBulkSlotData(array $data): void
    {
        $required = ['start_date', 'end_date', 'pattern', 'time_slots', 'type'];

        foreach ($required as $field) {
            if (! isset($data[$field])) {
                throw ValidationException::withMessages([
                    $field => "The {$field} field is required for bulk creation.",
                ]);
            }
        }

        if (! is_array($data['time_slots']) || empty($data['time_slots'])) {
            throw ValidationException::withMessages([
                'time_slots' => 'At least one time slot is required.',
            ]);
        }

        if (! in_array($data['pattern'], ['daily', 'weekly', 'weekdays', 'weekends'])) {
            throw ValidationException::withMessages([
                'pattern' => 'Pattern must be one of: daily, weekly, weekdays, weekends.',
            ]);
        }
    }

    /**
     * Check for time overlaps (throws exception)
     */
    protected function validateNoOverlap(Doctor $doctor, array $slotData, ?int $excludeSlotId = null): void
    {
        if ($this->hasOverlap($doctor, $slotData, $excludeSlotId)) {
            throw ValidationException::withMessages([
                'time' => 'This time slot overlaps with an existing appointment.',
            ]);
        }
    }

    /**
     * Check if slot data has overlap with existing slots
     */
    protected function hasOverlap(Doctor $doctor, array $slotData, ?int $excludeSlotId = null): bool
    {
        $query = $doctor->slots()
            ->forDate($slotData['date'])
            ->where(function ($q) use ($slotData) {
                $q->where(function ($subQ) use ($slotData) {
                    $subQ->where('start_time', '<=', $slotData['start_time'])
                        ->where('end_time', '>', $slotData['start_time']);
                })->orWhere(function ($subQ) use ($slotData) {
                    $subQ->where('start_time', '<', $slotData['end_time'])
                        ->where('end_time', '>=', $slotData['end_time']);
                })->orWhere(function ($subQ) use ($slotData) {
                    $subQ->where('start_time', '>=', $slotData['start_time'])
                        ->where('end_time', '<=', $slotData['end_time']);
                });
            });

        if ($excludeSlotId) {
            $query->where('id', '!=', $excludeSlotId);
        }

        return $query->exists();
    }

    /**
     * Generate recurring dates based on pattern
     */
    protected function generateRecurringDates(array $bulkData): Collection
    {
        $startDate = Carbon::parse($bulkData['start_date']);
        $endDate = Carbon::parse($bulkData['end_date']);
        $pattern = $bulkData['pattern'];
        $dates = collect();

        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $shouldInclude = match ($pattern) {
                'daily' => true,
                'weekly' => $current->eq($startDate) || $current->diffInDays($startDate) % 7 === 0,
                'weekdays' => $current->isWeekday(),
                'weekends' => $current->isWeekend(),
                default => false,
            };

            if ($shouldInclude) {
                $dates->push($current->format('Y-m-d'));
            }

            $current->addDay();
        }

        return $dates;
    }

    /**
     * Create Zoom meeting for a slot
     */
    protected function createZoomMeetingForSlot(Slot $slot): void
    {
        try {
            $topic = "Consultation with Dr. {$slot->doctor->user->name}";
            $startTime = $slot->date->format('Y-m-d').'T'.$slot->start_time.':00';
            $duration = (int) Carbon::parse($slot->end_time)->diffInMinutes(Carbon::parse($slot->start_time));

            $meeting = $this->zoomService->createMeetingForAppointment(
                $slot->doctor->user->email,
                $topic,
                $startTime,
                $duration,
                $slot
            );

            $slot->update([
                'zoom_meeting_id' => $meeting['id'],
                'zoom_join_url' => $meeting['join_url'],
                'zoom_start_url' => $meeting['start_url'],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create Zoom meeting for slot', [
                'slot_id' => $slot->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update Zoom meeting for a slot
     */
    protected function updateZoomMeetingForSlot(Slot $slot): void
    {
        try {
            $topic = "Consultation with Dr. {$slot->doctor->user->name}";
            $startTime = $slot->date->format('Y-m-d').'T'.$slot->start_time.':00';
            $duration = (int) Carbon::parse($slot->end_time)->diffInMinutes(Carbon::parse($slot->start_time));

            $this->zoomService->updateMeeting(
                $slot->zoom_meeting_id,
                $topic,
                $startTime,
                $duration
            );

        } catch (\Exception $e) {
            Log::error('Failed to update Zoom meeting for slot', [
                'slot_id' => $slot->id,
                'zoom_meeting_id' => $slot->zoom_meeting_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete Zoom meeting for a slot
     */
    protected function deleteZoomMeetingForSlot(Slot $slot): void
    {
        try {
            if ($slot->zoom_meeting_id) {
                $this->zoomService->deleteMeeting($slot->zoom_meeting_id);

                $slot->update([
                    'zoom_meeting_id' => null,
                    'zoom_join_url' => null,
                    'zoom_start_url' => null,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to delete Zoom meeting for slot', [
                'slot_id' => $slot->id,
                'zoom_meeting_id' => $slot->zoom_meeting_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update Zoom meeting settings for a slot
     */
    public function updateZoomMeetingSettings(Slot $slot, array $settings): bool
    {
        if (! $slot->hasZoomMeeting()) {
            throw ValidationException::withMessages([
                'zoom' => 'No Zoom meeting exists for this appointment.',
            ]);
        }

        try {
            $this->zoomService->updateMeetingSettings($slot->zoom_meeting_id, $settings);

            Log::info('Zoom meeting settings updated', [
                'slot_id' => $slot->id,
                'zoom_meeting_id' => $slot->zoom_meeting_id,
                'settings' => $settings,
            ]);

            if ($slot->booking && $this->settingsAffectPatient($settings)) {
                $this->notificationService->notifyZoomMeetingUpdate($slot);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to update Zoom meeting settings', [
                'slot_id' => $slot->id,
                'zoom_meeting_id' => $slot->zoom_meeting_id,
                'settings' => $settings,
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'zoom' => 'Failed to update Zoom meeting settings: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Recreate Zoom meeting for a slot (fallback option)
     */
    public function recreateZoomMeeting(Slot $slot): bool
    {
        if ($slot->type !== 'online') {
            throw ValidationException::withMessages([
                'type' => 'Can only recreate Zoom meetings for online appointments.',
            ]);
        }

        return DB::transaction(function () use ($slot) {
            if ($slot->hasZoomMeeting()) {
                $this->deleteZoomMeetingForSlot($slot);
            }

            $this->createZoomMeetingForSlot($slot);

            if (! $slot->fresh()->hasZoomMeeting()) {
                throw ValidationException::withMessages([
                    'zoom' => 'Failed to recreate Zoom meeting. Please try again or create manually.',
                ]);
            }

            if ($slot->booking) {
                $this->notificationService->notifyZoomMeetingUpdate($slot);
            }

            Log::info('Zoom meeting recreated', [
                'slot_id' => $slot->id,
                'new_meeting_id' => $slot->fresh()->zoom_meeting_id,
            ]);

            return true;
        });
    }

    /**
     * Get Zoom meeting status for a slot
     */
    public function getZoomMeetingStatus(Slot $slot): array
    {
        if (! $slot->hasZoomMeeting()) {
            return [
                'exists' => false,
                'status' => 'missing',
                'message' => 'No Zoom meeting exists for this appointment',
            ];
        }

        try {
            $status = $this->zoomService->getMeetingStatus($slot->zoom_meeting_id);

            return [
                'exists' => $status['exists'],
                'status' => $status['status'],
                'start_time' => $status['start_time'] ?? null,
                'join_url' => $status['join_url'] ?? $slot->zoom_join_url,
                'start_url' => $status['start_url'] ?? $slot->zoom_start_url,
                'message' => $this->getStatusMessage($status['status']),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get Zoom meeting status', [
                'slot_id' => $slot->id,
                'zoom_meeting_id' => $slot->zoom_meeting_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'exists' => false,
                'status' => 'error',
                'message' => 'Unable to check meeting status: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check if settings changes affect the patient experience
     */
    protected function settingsAffectPatient(array $settings): bool
    {
        $patientAffectingSettings = [
            'waiting_room',
            'join_before_host',
            'mute_upon_entry',
            'video',
            'password_protection',
        ];

        return collect($patientAffectingSettings)->some(fn ($setting) => isset($settings[$setting]));
    }

    /**
     * Get human-readable status message
     */
    protected function getStatusMessage(string $status): string
    {
        return match ($status) {
            'waiting' => 'Meeting is scheduled and ready',
            'started' => 'Meeting is currently active',
            'ended' => 'Meeting has ended',
            'not_found' => 'Meeting not found on Zoom',
            'error' => 'Error checking meeting status',
            default => 'Meeting status unknown',
        };
    }
}
