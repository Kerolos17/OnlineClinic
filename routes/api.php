<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\SpecializationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Specializations
    Route::get('/specializations', [SpecializationController::class, 'index']);

    // Doctors
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{id}', [DoctorController::class, 'show']);
    Route::get('/doctors/{id}/available-dates', [DoctorController::class, 'availableDates']);
    Route::get('/doctors/{id}/slots', [DoctorController::class, 'slots']);

    // Booking
    Route::post('/bookings/validate-slot', [BookingController::class, 'validateSlot'])
        ->middleware('throttle:30,1'); // 30 checks per minute
    Route::post('/bookings', [BookingController::class, 'store'])
        ->middleware('throttle:5,1'); // 5 bookings per minute
    Route::get('/bookings/{id}/status', [BookingController::class, 'status']);
});
