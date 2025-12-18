<?php

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;

test('can list doctors via api', function () {
    $specialization = Specialization::factory()->create();
    $user = User::factory()->create();
    Doctor::factory()->count(5)->create([
        'specialization_id' => $specialization->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/v1/doctors');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'specialization_id',
                    'consultation_price',
                    'rating',
                    'is_active',
                ],
            ],
        ]);
});

test('can filter doctors by specialization', function () {
    $specialization1 = Specialization::factory()->create();
    $specialization2 = Specialization::factory()->create();
    $user = User::factory()->create();

    Doctor::factory()->count(3)->create([
        'specialization_id' => $specialization1->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    Doctor::factory()->count(2)->create([
        'specialization_id' => $specialization2->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $response = $this->getJson("/api/v1/doctors?specialization={$specialization1->id}");

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(3);
});

test('can get doctor details via api', function () {
    $specialization = Specialization::factory()->create();
    $user = User::factory()->create();
    $doctor = Doctor::factory()->create([
        'specialization_id' => $specialization->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $response = $this->getJson("/api/v1/doctors/{$doctor->id}");

    $response->assertStatus(200)
        ->assertJson([
            'id' => $doctor->id,
            'user_id' => $user->id,
            'specialization_id' => $specialization->id,
        ]);
});

test('returns 404 for non-existent doctor', function () {
    $response = $this->getJson('/api/v1/doctors/99999');

    $response->assertStatus(404);
});
