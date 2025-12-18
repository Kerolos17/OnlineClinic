<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\Slot;
use App\Services\CacheService;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::active()->withRelations();
        
        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('specialization') && $request->specialization) {
            $query->bySpecialization($request->specialization);
        }
        
        // Order and paginate
        $doctors = $query->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->paginate(12);
            
        $allSpecializations = CacheService::getSpecializations();
        
        return view('doctors.index', compact('doctors', 'allSpecializations'));
    }
    
    public function show($id)
    {
        // Use cached doctor data
        $doctor = CacheService::getDoctor($id);
        
        return view('doctors.show', compact('doctor'));
    }
    
    public function getSlots(Request $request, $doctorId)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        
        // Validate date
        $request->validate([
            'date' => 'nullable|date|after_or_equal:today',
        ]);
        
        // Use cached slots
        $slots = CacheService::getDoctorSlots($doctorId, $date);
        
        return response()->json([
            'slots' => $slots,
            'date' => $date,
            'count' => $slots->count(),
        ]);
    }
}
