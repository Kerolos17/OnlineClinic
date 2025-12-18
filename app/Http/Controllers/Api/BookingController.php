<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Jobs\CreateZoomMeeting;
use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\Slot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function validateSlot(Request $request): JsonResponse
    {
        $request->validate([
            'slot_id' => 'required|exists:slots,id',
        ]);

        $slot = Slot::with('doctor.user')->find($request->slot_id);

        if (! $slot) {
            return response()->json([
                'available' => false,
                'message' => 'Slot not found',
            ], 404);
        }

        $isAvailable = $slot->status === 'available' && $slot->date->isFuture();

        return response()->json([
            'available' => $isAvailable,
            'slot' => $isAvailable ? [
                'id' => $slot->id,
                'date' => $slot->date->format('Y-m-d'),
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'doctor' => [
                    'id' => $slot->doctor->id,
                    'name' => $slot->doctor->user->name_en,
                    'name_ar' => $slot->doctor->user->name_ar,
                    'price' => $slot->doctor->consultation_price,
                ],
            ] : null,
        ]);
    }

    public function store(BookingRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $slot = Slot::lockForUpdate()->find($request->slot_id);

            // Double-check availability with lock
            if (! $slot || $slot->status !== 'available') {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() === 'ar'
                        ? 'عذراً، هذا الموعد لم يعد متاحاً'
                        : 'Sorry, this slot is no longer available',
                ], 422);
            }

            // Create booking
            $booking = Booking::create([
                'doctor_id' => $request->doctor_id,
                'slot_id' => $request->slot_id,
                'patient_name' => $request->patient_name,
                'patient_email' => $request->patient_email,
                'patient_phone' => $request->patient_phone,
                'patient_notes' => $request->patient_notes,
                'status' => 'pending',
                'amount' => $slot->doctor->consultation_price,
                'appointment_at' => $slot->date->format('Y-m-d').' '.$slot->start_time,
            ]);

            // Mark slot as booked
            $slot->markAsBooked();

            DB::commit();

            // Dispatch Zoom meeting creation job
            CreateZoomMeeting::dispatch($booking);

            // Send confirmation email
            try {
                Mail::to($booking->patient_email)
                    ->send(new BookingConfirmation($booking));
            } catch (\Exception $e) {
                Log::error('Failed to send booking confirmation email', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }

            Log::info('Booking created successfully', [
                'booking_id' => $booking->id,
                'doctor_id' => $booking->doctor_id,
                'patient_email' => $booking->patient_email,
            ]);

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم الحجز بنجاح'
                    : 'Booking created successfully',
                'booking' => [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'appointment_at' => $booking->appointment_at,
                ],
                'redirect_url' => route('booking.success', $booking->id),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'حدث خطأ أثناء الحجز. يرجى المحاولة مرة أخرى'
                    : 'An error occurred while creating the booking. Please try again',
            ], 500);
        }
    }

    public function status(int $id): JsonResponse
    {
        $booking = Booking::with(['doctor.user', 'slot'])->find($id);

        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'appointment_at' => $booking->appointment_at,
                'zoom_join_url' => $booking->zoom_join_url,
                'doctor' => [
                    'name' => $booking->doctor->user->name_en,
                    'name_ar' => $booking->doctor->user->name_ar,
                ],
            ],
        ]);
    }
}
