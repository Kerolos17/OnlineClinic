<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $specializations = \App\Models\Specialization::where('is_active', true)->get();
        $topDoctors = \App\Models\Doctor::where('is_active', true)
            ->orderBy('rating', 'desc')
            ->take(6)
            ->with(['user', 'specialization'])
            ->get();
        
        // Dynamic statistics
        $stats = [
            'total_doctors' => \App\Models\Doctor::where('is_active', true)->count(),
            'total_bookings' => \App\Models\Booking::count(),
            'average_rating' => round(\App\Models\Doctor::where('is_active', true)->avg('rating'), 1),
            'satisfaction_rate' => \App\Models\Booking::where('status', 'completed')->count() > 0 
                ? round((\App\Models\Booking::where('status', 'completed')->count() / \App\Models\Booking::whereIn('status', ['completed', 'cancelled'])->count()) * 100)
                : 98,
        ];
        
        return view('home', compact('specializations', 'topDoctors', 'stats'));
    }
    
    public function switchLanguage($lang)
    {
        if (in_array($lang, ['en', 'ar'])) {
            session(['locale' => $lang]);
            app()->setLocale($lang);
        }
        
        return redirect()->back();
    }
}
