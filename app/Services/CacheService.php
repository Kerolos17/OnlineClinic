<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Specialization;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache duration in seconds (1 hour)
     */
    const CACHE_DURATION = 3600;

    /**
     * Get all active specializations with caching
     */
    public static function getSpecializations()
    {
        return Cache::remember('specializations.active', self::CACHE_DURATION, function () {
            return Specialization::active()
                ->withDoctorsCount()
                ->orderBy('name_en')
                ->get();
        });
    }

    /**
     * Get specialization by ID with caching
     */
    public static function getSpecialization($id)
    {
        return Cache::remember("specialization.{$id}", self::CACHE_DURATION, function () use ($id) {
            return Specialization::with('activeDoctors.user')
                ->findOrFail($id);
        });
    }

    /**
     * Get all active doctors with caching
     */
    public static function getDoctors($specializationId = null)
    {
        $cacheKey = $specializationId 
            ? "doctors.specialization.{$specializationId}" 
            : 'doctors.all';

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($specializationId) {
            $query = Doctor::active()->withRelations();

            if ($specializationId) {
                $query->bySpecialization($specializationId);
            }

            return $query->orderBy('rating', 'desc')
                ->orderBy('total_reviews', 'desc')
                ->get();
        });
    }

    /**
     * Get doctor by ID with caching
     */
    public static function getDoctor($id)
    {
        return Cache::remember("doctor.{$id}", self::CACHE_DURATION, function () use ($id) {
            return Doctor::with([
                'user',
                'specialization',
                'approvedReviews' => function ($query) {
                    $query->latest()->limit(10);
                }
            ])->findOrFail($id);
        });
    }

    /**
     * Get available slots for a doctor on a specific date
     */
    public static function getDoctorSlots($doctorId, $date)
    {
        $cacheKey = "doctor.{$doctorId}.slots.{$date}";
        
        // Cache for shorter duration (15 minutes) as slots change frequently
        return Cache::remember($cacheKey, 900, function () use ($doctorId, $date) {
            return \App\Models\Slot::forDoctor($doctorId)
                ->forDate($date)
                ->available()
                ->ordered()
                ->get();
        });
    }

    /**
     * Clear all doctor-related caches
     */
    public static function clearDoctorCache($doctorId = null)
    {
        if ($doctorId) {
            Cache::forget("doctor.{$doctorId}");
            Cache::forget("doctors.specialization.*");
            
            // Clear slots cache for this doctor
            for ($i = 0; $i < 30; $i++) {
                $date = now()->addDays($i)->format('Y-m-d');
                Cache::forget("doctor.{$doctorId}.slots.{$date}");
            }
        }
        
        Cache::forget('doctors.all');
    }

    /**
     * Clear all specialization-related caches
     */
    public static function clearSpecializationCache($specializationId = null)
    {
        if ($specializationId) {
            Cache::forget("specialization.{$specializationId}");
            Cache::forget("doctors.specialization.{$specializationId}");
        }
        
        Cache::forget('specializations.active');
    }

    /**
     * Clear slot cache for a specific doctor and date
     */
    public static function clearSlotCache($doctorId, $date)
    {
        Cache::forget("doctor.{$doctorId}.slots.{$date}");
    }

    /**
     * Clear all caches
     */
    public static function clearAll()
    {
        Cache::flush();
    }

    /**
     * Warm up cache with frequently accessed data
     */
    public static function warmUp()
    {
        // Cache specializations
        self::getSpecializations();

        // Cache all doctors
        self::getDoctors();

        // Cache top doctors
        $topDoctors = Doctor::active()
            ->withRelations()
            ->orderBy('rating', 'desc')
            ->limit(10)
            ->get();

        foreach ($topDoctors as $doctor) {
            self::getDoctor($doctor->id);
        }
    }
}
