<?php

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $specialization = Specialization::create([
        'name_en' => 'Cardiology',
        'name_ar' => 'أمراض القلب',
        'icon' => '❤️',
        'is_active' => true,
    ]);

    $user = User::create([
        'name_en' => 'Dr. John Doe',
        'name_ar' => 'د. جون دو',
        'email' => 'doctor@example.com',
        'password' => bcrypt('password'),
        'role' => 'doctor',
    ]);

    $this->doctor = Doctor::create([
        'user_id' => $user->id,
        'specialization_id' => $specialization->id,
        'bio' => ['en' => 'Experienced cardiologist', 'ar' => 'طبيب قلب ذو خبرة'],
        'experience_years' => 10,
        'languages' => ['English', 'Arabic'],
        'consultation_price' => 100.00,
        'rating' => 4.5,
        'total_reviews' => 50,
        'is_active' => true,
    ]);
});

test('doctor belongs to user', function () {
    expect($this->doctor->user)->toBeInstanceOf(User::class)
        ->and($this->doctor->user->email)->toBe('doctor@example.com');
});

test('doctor belongs to specialization', function () {
    expect($this->doctor->specialization)->toBeInstanceOf(Specialization::class)
        ->and($this->doctor->specialization->name_en)->toBe('Cardiology');
});

test('doctor has many bookings', function () {
    expect($this->doctor->bookings)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});

test('doctor has many reviews', function () {
    expect($this->doctor->reviews)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});

test('doctor has many slots', function () {
    expect($this->doctor->slots)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});

test('active scope returns only active doctors', function () {
    Doctor::create([
        'user_id' => User::create([
            'name_en' => 'Dr. Jane Smith',
            'name_ar' => 'د. جين سميث',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'role' => 'doctor',
        ])->id,
        'specialization_id' => $this->doctor->specialization_id,
        'experience_years' => 5,
        'languages' => ['English'],
        'consultation_price' => 80.00,
        'is_active' => false,
    ]);

    $activeDoctors = Doctor::active()->get();

    expect($activeDoctors)->toHaveCount(1)
        ->and($activeDoctors->first()->is_active)->toBeTrue();
});

test('by specialization scope filters correctly', function () {
    $newSpec = Specialization::create([
        'name_en' => 'Dermatology',
        'name_ar' => 'الأمراض الجلدية',
        'icon' => '🩺',
        'is_active' => true,
    ]);

    Doctor::create([
        'user_id' => User::create([
            'name_en' => 'Dr. Jane Smith',
            'name_ar' => 'د. جين سميث',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'role' => 'doctor',
        ])->id,
        'specialization_id' => $newSpec->id,
        'experience_years' => 5,
        'languages' => ['English'],
        'consultation_price' => 80.00,
        'is_active' => true,
    ]);

    $cardiologists = Doctor::bySpecialization($this->doctor->specialization_id)->get();

    expect($cardiologists)->toHaveCount(1)
        ->and($cardiologists->first()->specialization->name_en)->toBe('Cardiology');
});

test('full name accessor returns correct name based on locale', function () {
    app()->setLocale('en');
    expect($this->doctor->full_name)->toBe('Dr. John Doe');

    app()->setLocale('ar');
    expect($this->doctor->full_name)->toBe('د. جون دو');
});

test('bio is cast to array', function () {
    expect($this->doctor->bio)->toBeArray()
        ->toHaveKey('en')
        ->toHaveKey('ar');
});

test('languages is cast to array', function () {
    expect($this->doctor->languages)->toBeArray()
        ->toContain('English')
        ->toContain('Arabic');
});

test('consultation price is cast to decimal', function () {
    expect($this->doctor->consultation_price)->toBe('100.00');
});

test('rating is cast to decimal', function () {
    expect($this->doctor->rating)->toBe('4.50');
});

test('is active is cast to boolean', function () {
    expect($this->doctor->is_active)->toBeBool()->toBeTrue();
});
