<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Language Switcher
Route::get('/lang/{lang}', [HomeController::class, 'switchLanguage'])->name('lang.switch');

// Doctors
Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
Route::get('/doctors/{id}', [DoctorController::class, 'show'])->name('doctors.show');

// Specializations
Route::get('/specializations', [\App\Http\Controllers\SpecializationController::class, 'index'])->name('specializations.index');
Route::get('/specializations/{id}', [\App\Http\Controllers\SpecializationController::class, 'show'])->name('specializations.show');

// Booking
Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/{id}/success', [BookingController::class, 'success'])->name('booking.success');

// Reviews
Route::post('/doctors/{doctor}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

// Static Pages
Route::get('/about', [\App\Http\Controllers\PageController::class, 'about'])->name('about');
Route::get('/contact', [\App\Http\Controllers\PageController::class, 'contact'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\PageController::class, 'contactSubmit'])->name('contact.submit');

// API Routes for AJAX
Route::get('/api/doctors/{id}/slots', [DoctorController::class, 'getSlots']);
