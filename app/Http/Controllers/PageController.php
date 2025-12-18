<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function about()
    {
        $stats = [
            'doctors_count' => \App\Models\Doctor::count(),
            'specializations_count' => \App\Models\Specialization::count(),
            'completed_bookings' => \App\Models\Booking::where('status', 'completed')->count(),
            'total_bookings' => \App\Models\Booking::count(),
        ];

        // Calculate satisfaction rate (completed bookings / total bookings * 100)
        $stats['satisfaction_rate'] = $stats['total_bookings'] > 0
            ? round(($stats['completed_bookings'] / $stats['total_bookings']) * 100)
            : 0;

        return view('pages.about', compact('stats'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Add timestamp
        $validated['submitted_at'] = now()->format('Y-m-d H:i:s');

        // Send email to admin
        try {
            Mail::to(config('mail.from.address'))
                ->send(new ContactFormMail($validated));
        } catch (\Exception $e) {
            \Log::error('Contact form email failed: '.$e->getMessage());

            return redirect()->route('contact')
                ->with('error', app()->getLocale() == 'ar'
                    ? 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.'
                    : 'An error occurred while sending the message. Please try again.');
        }

        return redirect()->route('contact')
            ->with('success', app()->getLocale() == 'ar'
                ? 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً!'
                : 'Your message has been sent successfully. We will contact you soon!');
    }
}
