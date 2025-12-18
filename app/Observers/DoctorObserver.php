<?php

namespace App\Observers;

use App\Models\Doctor;
use Illuminate\Support\Facades\Cache;

class DoctorObserver
{
    public function created(Doctor $doctor): void
    {
        $this->clearCache($doctor);
    }

    public function updated(Doctor $doctor): void
    {
        $this->clearCache($doctor);
    }

    public function deleted(Doctor $doctor): void
    {
        $this->clearCache($doctor);
    }

    private function clearCache(Doctor $doctor): void
    {
        // Clear doctor-specific cache
        // Note: Using individual keys instead of tags for database/file cache compatibility
        Cache::forget("api.doctor.{$doctor->id}");
        Cache::forget("api.doctors.*");
        Cache::forget("api.doctor.{$doctor->id}.dates.*");
        Cache::forget("api.doctor.{$doctor->id}.slots.*");
        
        // If using Redis, you can use tags instead:
        // Cache::tags(['doctors', "doctor:{$doctor->id}"])->flush();
    }
}
