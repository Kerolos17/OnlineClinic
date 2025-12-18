<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DoctorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'api.doctors.'.md5(json_encode($request->all()));

        $doctors = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Doctor::active()
                ->withRelations();

            // Filters
            if ($request->has('specialization')) {
                $query->bySpecialization($request->specialization);
            }

            if ($request->has('min_rating')) {
                $query->withMinRating($request->min_rating);
            }

            if ($request->has('language')) {
                $query->byLanguage($request->language);
            }

            if ($request->has('min_price') || $request->has('max_price')) {
                $query->byPriceRange($request->min_price, $request->max_price);
            }

            if ($request->has('search')) {
                $query->search($request->search);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'rating');
            $sortOrder = $request->get('sort_order', 'desc');

            if ($sortBy === 'rating') {
                $query->orderBy('rating', $sortOrder)
                    ->orderBy('total_reviews', 'desc');
            } elseif ($sortBy === 'price') {
                $query->orderBy('consultation_price', $sortOrder);
            } elseif ($sortBy === 'experience') {
                $query->orderBy('experience_years', $sortOrder);
            }

            return $query->paginate($request->get('per_page', 12));
        });

        return response()->json($doctors);
    }

    public function show(int $id): JsonResponse
    {
        $doctor = Cache::remember(
            "api.doctor.{$id}",
            300,
            fn () => Doctor::with(['user', 'specialization', 'approvedReviews.user'])
                ->findOrFail($id)
        );

        return response()->json($doctor);
    }

    public function availableDates(int $id, Request $request): JsonResponse
    {
        $doctor = Doctor::findOrFail($id);
        $startDate = Carbon::parse($request->get('start_date', now()));
        $endDate = $startDate->copy()->addDays($request->get('days', 30));

        $cacheKey = "api.doctor.{$id}.dates.{$startDate->format('Y-m-d')}.{$endDate->format('Y-m-d')}";

        $dates = Cache::remember($cacheKey, 300, function () use ($doctor, $startDate, $endDate) {
            return Slot::forDoctor($doctor->id)
                ->available()
                ->betweenDates($startDate, $endDate)
                ->future()
                ->get()
                ->groupBy(fn ($slot) => $slot->date->format('Y-m-d'))
                ->map(fn ($slots) => [
                    'date' => $slots->first()->date->format('Y-m-d'),
                    'available_slots' => $slots->count(),
                    'slots' => $slots->map(fn ($slot) => [
                        'id' => $slot->id,
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                    ]),
                ])
                ->values();
        });

        return response()->json([
            'doctor_id' => $doctor->id,
            'dates' => $dates,
        ]);
    }

    public function slots(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $doctor = Doctor::findOrFail($id);
        $date = Carbon::parse($request->date);

        $cacheKey = "api.doctor.{$id}.slots.{$date->format('Y-m-d')}";

        $slots = Cache::remember($cacheKey, 60, function () use ($doctor, $date) {
            return Slot::forDoctor($doctor->id)
                ->forDate($date)
                ->available()
                ->ordered()
                ->get()
                ->map(fn ($slot) => [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'status' => $slot->status,
                ]);
        });

        return response()->json([
            'doctor_id' => $doctor->id,
            'date' => $date->format('Y-m-d'),
            'slots' => $slots,
        ]);
    }
}
