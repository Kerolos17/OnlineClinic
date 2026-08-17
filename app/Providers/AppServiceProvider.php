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
use App\Providers\Filament\DoctorPanelProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the Doctor Panel Provider
        $this->app->register(DoctorPanelProvider::class);

        // Register PatientNotificationService
        $this->app->singleton(\App\Services\PatientNotificationService::class);

        // Register PaymentService (simulated payment gateway)
        $this->app->singleton(\App\Services\PaymentService::class);

        // Register DoctorSlotService with dependencies
        $this->app->singleton(\App\Services\DoctorSlotService::class, function ($app) {
            return new \App\Services\DoctorSlotService(
                $app->make(\App\Services\ZoomService::class),
                $app->make(\App\Services\PatientNotificationService::class)
            );
        });
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
