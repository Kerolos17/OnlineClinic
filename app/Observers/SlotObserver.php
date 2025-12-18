<?php

namespace App\Observers;

use App\Models\Slot;
use Illuminate\Support\Facades\Cache;

class SlotObserver
{
    public function created(Slot $slot): void
    {
        $this->clearCache($slot);
    }

    public function updated(Slot $slot): void
    {
        $this->clearCache($slot);
    }

    public function deleted(Slot $slot): void
    {
        $this->clearCache($slot);
    }

    private function clearCache(Slot $slot): void
    {
        // Clear slots cache for this doctor
        // Note: Using individual keys instead of tags for database/file cache compatibility
        Cache::forget("api.doctor.{$slot->doctor_id}.dates.*");
        Cache::forget("api.doctor.{$slot->doctor_id}.slots.*");
        
        // If using Redis, you can use tags instead:
        // Cache::tags(["slots:doctor:{$slot->doctor_id}"])->flush();
    }
}
