<?php

namespace App\Http\Controllers;

use App\Jobs\CreateZoomMeeting;
use App\Mail\BookingConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $doctor = \App\Models\Doctor::with(['user', 'specialization'])->findOrFail($request->doctor);
        $slot = \App\Models\Slot::where('status', 'available')->findOrFail($request->slot);

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

        $doctor = \App\Models\Doctor::findOrFail($validated['doctor_id']);
        $slot = \App\Models\Slot::where('status', 'available')->findOrFail($validated['slot_id']);

        // إنشاء الحجز
        $booking = \App\Models\Booking::create([
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

        // إنشاء سجل الدفع
        \App\Models\Payment::create([
            'booking_id' => $booking->id,
            'payment_provider' => 'stripe',
            'transaction_id' => 'TXN_'.uniqid(),
            'amount' => $booking->amount,
            'currency' => 'EGY',
            'status' => 'completed',
        ]);

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


        // ✅ تشغيل الاجتماع فورًا في وضع التطوير فقط (للتجربة)
        // if (app()->environment('local')) {
        //     try {
        //         CreateZoomMeeting::dispatch($booking);
        //         \Log::info("Zoom meeting created immediately for booking {$booking->id} (local environment)");
        //     } catch (\Exception $e) {
        //         \Log::error("Failed to dispatch immediate Zoom job for booking {$booking->id}: " . $e->getMessage());
        //     }
        // }

        return redirect()->route('booking.success', $booking->id);
    }

    public function success($id)
    {
        $booking = \App\Models\Booking::with(['doctor.user', 'doctor.specialization'])->findOrFail($id);

        return view('booking.success', compact('booking'));
    }
}
