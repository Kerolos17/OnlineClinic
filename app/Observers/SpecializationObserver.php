<?php

namespace App\Observers;

use App\Models\Specialization;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class SpecializationObserver
{
    public function created(Specialization $specialization): void
    {
        $this->clearCache($specialization);
    }

    public function updated(Specialization $specialization): void
    {
        $this->clearCache($specialization);
    }

    public function deleted(Specialization $specialization): void
    {
        $this->clearCache($specialization);
    }

    private function clearCache(Specialization $specialization): void
    {
        // Clear CacheService keys (web public cache)
        CacheService::clearSpecializationCache($specialization->id);

        // Clear specializations cache
        Cache::forget('api.specializations');
        
        // If using Redis, you can use tags instead:
        // Cache::tags(['specializations'])->flush();
    }
}
