<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\Slot;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $specializationId = $request->input('specialization');

        if ($search || $specializationId) {
            // Filtered query - direct (filters are user-specific, not cached)
            $query = Doctor::active()->withRelations();

            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                      ->orWhere('name_ar', 'like', "%{$search}%");
                });
            }

            if ($specializationId) {
                $query->bySpecialization($specializationId);
            }

            $doctors = $query->orderBy('rating', 'desc')
                ->orderBy('total_reviews', 'desc')
                ->paginate(12);
        } else {
            // Unfiltered list - use cached doctors
            $cachedDoctors = CacheService::getDoctors();

            $page = max(1, (int) $request->input('page', 1));
            $perPage = 12;

            $doctors = new LengthAwarePaginator(
                $cachedDoctors->forPage($page, $perPage)->values(),
                $cachedDoctors->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

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
