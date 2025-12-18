<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        $validated['doctor_id'] = $doctor->id;
        $validated['status'] = 'pending';

        Review::create($validated);

        return redirect()
            ->route('doctors.show', $doctor)
            ->with('success', app()->getLocale() == 'ar'
                ? 'شكراً لتقييمك! سيتم مراجعته قريباً.'
                : 'Thank you for your review! It will be reviewed soon.');
    }
}
