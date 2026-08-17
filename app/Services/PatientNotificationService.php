<?php

namespace App\Services;

use App\Mail\BookingUpdateMail;
use App\Models\Slot;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PatientNotificationService
{
    /**
     * Notify patient about appointment cancellation
     */
    public function notifyAppointmentCancellation(Slot $slot): void
    {
        if (! $slot->booking) {
            return;
        }

        try {
            Mail::to($slot->booking->patient_email)
                ->queue(new BookingUpdateMail($slot->booking, 'cancellation'));

            Log::info('Patient notification: Appointment cancelled', [
                'patient_name' => $slot->booking->patient_name,
                'patient_email' => $slot->booking->patient_email,
                'patient_phone' => $slot->booking->patient_phone,
                'appointment_date' => $slot->date->format('Y-m-d'),
                'appointment_time' => $slot->start_time.' - '.$slot->end_time,
                'notification_type' => 'cancellation',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send patient cancellation notification', [
                'error' => $e->getMessage(),
                'slot_id' => $slot->id,
            ]);
        }
    }

    /**
     * Notify patient about appointment modification
     */
    public function notifyAppointmentModification(Slot $slot, array $changes): void
    {
        if (! $slot->booking) {
            return;
        }

        try {
            Mail::to($slot->booking->patient_email)
                ->queue(new BookingUpdateMail($slot->booking, 'modification', $changes));

            Log::info('Patient notification: Appointment modified', [
                'patient_name' => $slot->booking->patient_name,
                'patient_email' => $slot->booking->patient_email,
                'patient_phone' => $slot->booking->patient_phone,
                'appointment_date' => $slot->date->format('Y-m-d'),
                'appointment_time' => $slot->start_time.' - '.$slot->end_time,
                'changes' => $changes,
                'notification_type' => 'modification',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send patient modification notification', [
                'error' => $e->getMessage(),
                'slot_id' => $slot->id,
                'changes' => $changes,
            ]);
        }
    }

    /**
     * Notify patient about Zoom meeting updates
     */
    public function notifyZoomMeetingUpdate(Slot $slot): void
    {
        if (! $slot->booking || ! $slot->isOnline()) {
            return;
        }

        try {
            Mail::to($slot->booking->patient_email)
                ->queue(new BookingUpdateMail($slot->booking, 'zoom_update'));

            Log::info('Patient notification: Zoom meeting updated', [
                'patient_name' => $slot->booking->patient_name,
                'patient_email' => $slot->booking->patient_email,
                'zoom_join_url' => $slot->zoom_join_url,
                'appointment_date' => $slot->date->format('Y-m-d'),
                'appointment_time' => $slot->start_time.' - '.$slot->end_time,
                'notification_type' => 'zoom_update',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send Zoom update notification', [
                'error' => $e->getMessage(),
                'slot_id' => $slot->id,
            ]);
        }
    }
}
