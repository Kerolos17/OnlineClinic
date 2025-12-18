<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Slot;
use App\Models\Specialization;
use App\Observers\BookingObserver;
use App\Observers\DoctorObserver;
use App\Observers\SlotObserver;
use App\Observers\SpecializationObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Model Observers for automatic cache invalidation
        Booking::observe(BookingObserver::class);
        Doctor::observe(DoctorObserver::class);
        Slot::observe(SlotObserver::class);
        Specialization::observe(SpecializationObserver::class);

        // Query monitoring in local environment
        if ($this->app->environment('local')) {
            \Illuminate\Support\Facades\DB::listen(function ($query) {
                if ($query->time > 100) {
                    \Illuminate\Support\Facades\Log::warning('Slow Query Detected', [
                        'sql' => $query->sql,
                        'time' => $query->time.'ms',
                        'bindings' => $query->bindings,
                    ]);
                }
            });
        }
    }
}
