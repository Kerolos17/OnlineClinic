<?php

namespace App\Http\Controllers;

use App\Jobs\CreateZoomMeeting;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Slot;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $doctor = Doctor::with(['user', 'specialization'])->findOrFail($request->doctor);
        $slot = Slot::where('status', 'available')->findOrFail($request->slot);

        return view('booking.create', compact('doctor', 'slot'));
    }

    public function store(Request $request)
    {
        $isArabic = app()->getLocale() == 'ar';

        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'slot_id' => 'required|exists:slots,id',
            'patient_name' => 'required|string|max:255|min:3',
            'patient_email' => 'required|email|max:255',
            'patient_phone' => 'required|string|min:10|max:20',
            'patient_notes' => 'nullable|string|max:1000',
        ], [
            'patient_name.required' => $isArabic ? 'الاسم مطلوب' : 'Name is required',
            'patient_name.min' => $isArabic ? 'الاسم يجب أن يكون 3 أحرف على الأقل' : 'Name must be at least 3 characters',
            'patient_name.max' => $isArabic ? 'الاسم طويل جداً' : 'Name is too long',
            'patient_email.required' => $isArabic ? 'البريد الإلكتروني مطلوب' : 'Email is required',
            'patient_email.email' => $isArabic ? 'البريد الإلكتروني غير صحيح' : 'Invalid email address',
            'patient_phone.required' => $isArabic ? 'رقم الهاتف مطلوب' : 'Phone number is required',
            'patient_phone.min' => $isArabic ? 'رقم الهاتف قصير جداً' : 'Phone number is too short',
            'patient_phone.max' => $isArabic ? 'رقم الهاتف طويل جداً' : 'Phone number is too long',
            'patient_notes.max' => $isArabic ? 'الملاحظات طويلة جداً (الحد الأقصى 1000 حرف)' : 'Notes are too long (max 1000 characters)',
        ]);

        // Atomic booking creation: lock the slot row so two concurrent requests
        // cannot both book the same slot.
        $booking = DB::transaction(function () use ($validated) {
            $slot = Slot::where('status', 'available')
                ->lockForUpdate()
                ->findOrFail($validated['slot_id']);

            $doctor = Doctor::findOrFail($validated['doctor_id']);

            // إنشاء الحجز
            $booking = Booking::create([
                'doctor_id' => $doctor->id,
                'slot_id' => $slot->id,
                'patient_name' => $validated['patient_name'],
                'patient_email' => $validated['patient_email'],
                'patient_phone' => $validated['patient_phone'],
                'patient_notes' => $validated['patient_notes'] ?? null,
                'status' => 'pending',
                'amount' => $doctor->consultation_price,
                'appointment_at' => $slot->date->format('Y-m-d').' '.$slot->start_time,
            ]);

            // تغيير حالة الـ Slot إلى محجوز
            $slot->update(['status' => 'booked']);

            // محاكاة نجاح الدفع (في البيئة الفعلية هيتم عبر بوابة الدفع)
            $booking->update(['status' => 'confirmed']);

            // إنشاء سجل الدفع عبر PaymentService
            $paymentResult = app(PaymentService::class)->charge($booking);

            Payment::create([
                'booking_id' => $booking->id,
                'payment_provider' => $paymentResult['provider'],
                'transaction_id' => $paymentResult['transaction_id'],
                'amount' => $booking->amount,
                'currency' => $paymentResult['currency'],
                'status' => $paymentResult['status'],
            ]);

            return $booking;
        });

        // إرسال إيميل تأكيد الحجز للطبيب والمريض فورًا
        Mail::to($booking->patient_email)->queue(new BookingConfirmationMail($booking, 'patient'));
        Mail::to($booking->doctor->user->email)->queue(new BookingConfirmationMail($booking, 'doctor'));

        // جدولة إنشاء اجتماع Zoom قبل الموعد بـ 30 دقيقة
        if ($booking->appointment_at && $booking->appointment_at->isFuture()) {
            // حساب الوقت اللي المفروض يتنفذ فيه الجوب
            $delayTime = $booking->appointment_at->copy()->subMinutes(30);

            // نتأكد إن الوقت لسه في المستقبل
            if ($delayTime->isFuture()) {
                CreateZoomMeeting::dispatch($booking)->delay($delayTime->timezone(config('app.timezone')));

                \Log::info("⏰ Zoom meeting job scheduled for booking {$booking->id} at {$delayTime}");
            } else {
                // لو باقي أقل من 30 دقيقة، نشغلها فورًا
                CreateZoomMeeting::dispatch($booking);
                \Log::info("⚡ Zoom meeting job dispatched immediately for booking {$booking->id}");
            }
        }

        return redirect()->route('booking.success', [
            'id' => $booking->id,
            'ref' => $booking->reference_code,
        ]);
    }

    public function success(Request $request, $id)
    {
        $booking = Booking::with(['doctor.user', 'doctor.specialization'])->findOrFail($id);

        // Protect the success page: require the booking reference code
        if (! $request->filled('ref') || ! hash_equals((string) $booking->reference_code, (string) $request->input('ref'))) {
            abort(404);
        }

        return view('booking.success', compact('booking'));
    }
}
