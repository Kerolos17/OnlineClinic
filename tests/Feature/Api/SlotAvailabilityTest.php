<?php

use App\Models\Doctor;
use App\Models\Slot;
use App\Models\Specialization;
use App\Models\User;
use Carbon\Carbon;

test('can get available dates for doctor', function () {
    $specialization = Specialization::factory()->create();
    $user = User::factory()->create();
    $doctor = Doctor::factory()->create([
        'specialization_id' => $specialization->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    // Create slots for next 3 days
    for ($i = 1; $i <= 3; $i++) {
        Slot::factory()->count(3)->create([
            'doctor_id' => $doctor->id,
            'date' => Carbon::now()->addDays($i),
            'status' => 'available',
        ]);
    }

    $response = $this->getJson("/api/v1/doctors/{$doctor->id}/available-dates");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'doctor_id',
            'dates' => [
                '*' => [
                    'date',
                    'available_slots',
                    'slots',
                ],
            ],
        ]);

    expect($response->json('dates'))->toHaveCount(3);
});

test('can get slots for specific date', function () {
    $specialization = Specialization::factory()->create();
    $user = User::factory()->create();
    $doctor = Doctor::factory()->create([
        'specialization_id' => $specialization->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $date = Carbon::tomorrow();

    Slot::factory()->count(5)->create([
        'doctor_id' => $doctor->id,
        'date' => $date,
        'status' => 'available',
    ]);

    $response = $this->getJson("/api/v1/doctors/{$doctor->id}/slots?date={$date->format('Y-m-d')}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'doctor_id',
            'date',
            'slots' => [
                '*' => [
                    'id',
                    'start_time',
                    'end_time',
                    'status',
                ],
            ],
        ]);

    expect($response->json('slots'))->toHaveCount(5);
});

test('does not return booked slots', function () {
    $specialization = Specialization::factory()->create();
    $user = User::factory()->create();
    $doctor = Doctor::factory()->create([
        'specialization_id' => $specialization->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $date = Carbon::tomorrow();

    // Create 3 available and 2 booked slots
    Slot::factory()->count(3)->create([
        'doctor_id' => $doctor->id,
        'date' => $date,
        'status' => 'available',
    ]);

    Slot::factory()->count(2)->create([
        'doctor_id' => $doctor->id,
        'date' => $date,
        'status' => 'booked',
    ]);

    $response = $this->getJson("/api/v1/doctors/{$doctor->id}/slots?date={$date->format('Y-m-d')}");

    $response->assertStatus(200);
    expect($response->json('slots'))->toHaveCount(3); // Only available slots
});

test('validates date parameter for slots endpoint', function () {
    $specialization = Specialization::factory()->create();
    $user = User::factory()->create();
    $doctor = Doctor::factory()->create([
        'specialization_id' => $specialization->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $response = $this->getJson("/api/v1/doctors/{$doctor->id}/slots?date=invalid-date");

    $response->assertStatus(422);
});

test('cannot get slots for past dates', function () {
    $specialization = Specialization::factory()->create();
    $user = User::factory()->create();
    $doctor = Doctor::factory()->create([
        'specialization_id' => $specialization->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $pastDate = Carbon::yesterday();

    $response = $this->getJson("/api/v1/doctors/{$doctor->id}/slots?date={$pastDate->format('Y-m-d')}");

    $response->assertStatus(422);
});
