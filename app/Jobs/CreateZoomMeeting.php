<?php

namespace App\Jobs;

use App\Mail\BookingReminderMail;
use App\Services\ZoomService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CreateZoomMeeting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new job instance.
     */
    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     */
    public function handle(ZoomService $zoom)
    {
        // تحميل بيانات الحجز والعلاقات المرتبطة
        $this->booking->load(['doctor.user', 'doctor.specialization']);

        try {
            // إنشاء اجتماع Zoom
            $meeting = $zoom->createMeeting(
                userId: 'me',
                topic: 'Consultation with Dr. ' . ($this->booking->doctor->name['en'] ?? 'Doctor') . 
                        ' - ' . $this->booking->patient_name,
                startTime: $this->booking->appointment_at->toIso8601String(),
                duration: 45
            );

            // تحديث الحجز ببيانات الاجتماع
            $this->booking->update([
                'zoom_meeting_id' => $meeting['id'] ?? null,
                'zoom_join_url' => $meeting['join_url'] ?? null,
                'zoom_start_url' => $meeting['start_url'] ?? null,
                'zoom_created_at' => now(),
            ]);

            // إرسال الإيميلات للطرفين (المريض والطبيب)
Mail::to($this->booking->patient_email)->queue(
    new BookingReminderMail($this->booking, 'patient')
);
Mail::to($this->booking->doctor->user->email)->queue(
    new BookingReminderMail($this->booking, 'doctor')
);


            // تسجيل العملية في اللوج
            \Log::info('✅ Zoom meeting created successfully for booking ' . $this->booking->id, [
                'meeting_id' => $meeting['id'] ?? null,
                'join_url' => $meeting['join_url'] ?? null,
                'start_url' => $meeting['start_url'] ?? null,
            ]);

        } catch (\Throwable $e) {
            // تسجيل أي أخطاء
            \Log::error('❌ Failed to create Zoom meeting for booking ' . $this->booking->id . ': ' . $e->getMessage());
            report($e);
        }
    }
}
