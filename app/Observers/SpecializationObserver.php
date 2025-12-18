<?php

namespace App\Observers;

use App\Models\Specialization;
use Illuminate\Support\Facades\Cache;

class SpecializationObserver
{
    public function created(Specialization $specialization): void
    {
        $this->clearCache();
    }

    public function updated(Specialization $specialization): void
    {
        $this->clearCache();
    }

    public function deleted(Specialization $specialization): void
    {
        $this->clearCache();
    }

    private function clearCache(): void
    {
        // Clear specializations cache
        Cache::forget('api.specializations');
        
        // If using Redis, you can use tags instead:
        // Cache::tags(['specializations'])->flush();
    }
}
