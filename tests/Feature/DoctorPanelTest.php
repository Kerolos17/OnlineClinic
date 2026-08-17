<?php

use App\Models\Doctor;
use App\Models\User;

test('doctor can access doctor panel', function () {
    // Create a doctor user
    $user = User::factory()->create([
        'role' => 'doctor',
        'email' => 'doctor@test.com',
    ]);

    $doctor = Doctor::factory()->create([
        'user_id' => $user->id,
    ]);

    // Act as the doctor user
    $this->actingAs($user);

    // Test that doctor can access the panel
    $response = $this->get('/doctor');

    $response->assertStatus(200);
});

test('non-doctor user cannot access doctor panel', function () {
    // Create a regular user (not a doctor)
    $user = User::factory()->create([
        'role' => 'patient',
        'email' => 'patient@test.com',
    ]);

    // Act as the non-doctor user
    $this->actingAs($user);

    // Test that non-doctor gets access denied
    $response = $this->get('/doctor');

    $response->assertStatus(403);
});

test('unauthenticated user is redirected to login', function () {
    // Test that unauthenticated user is redirected to login
    $response = $this->get('/doctor');

    $response->assertRedirect('/doctor/login');
});

test('doctor panel login page is accessible', function () {
    $response = $this->get('/doctor/login');

    $response->assertStatus(200);
});
test('doctor can view their own bookings', function () {
    // Create a doctor user
    $user = User::factory()->create([
        'role' => 'doctor',
        'email' => 'doctor@test.com',
    ]);

    $doctor = Doctor::factory()->create([
        'user_id' => $user->id,
    ]);

    // Create another doctor to ensure data isolation
    $otherUser = User::factory()->create([
        'role' => 'doctor',
        'email' => 'other-doctor@test.com',
    ]);

    $otherDoctor = Doctor::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    // Create slots and bookings for both doctors
    $doctorSlot = \App\Models\Slot::factory()->create([
        'doctor_id' => $doctor->id,
        'status' => 'booked',
    ]);

    $otherDoctorSlot = \App\Models\Slot::factory()->create([
        'doctor_id' => $otherDoctor->id,
        'status' => 'booked',
    ]);

    $doctorBooking = \App\Models\Booking::factory()->create([
        'doctor_id' => $doctor->id,
        'slot_id' => $doctorSlot->id,
        'patient_name' => 'John Doe',
        'status' => 'confirmed',
    ]);

    $otherDoctorBooking = \App\Models\Booking::factory()->create([
        'doctor_id' => $otherDoctor->id,
        'slot_id' => $otherDoctorSlot->id,
        'patient_name' => 'Jane Smith',
        'status' => 'confirmed',
    ]);

    // Act as the first doctor
    $this->actingAs($user);

    // Test that doctor can access bookings page
    $response = $this->get('/doctor/bookings');
    $response->assertStatus(200);

    // Test that doctor only sees their own bookings
    $response->assertSee('John Doe');
    $response->assertDontSee('Jane Smith');
});

test('doctor can view booking details', function () {
    // Create a doctor user
    $user = User::factory()->create([
        'role' => 'doctor',
        'email' => 'doctor@test.com',
    ]);

    $doctor = Doctor::factory()->create([
        'user_id' => $user->id,
    ]);

    // Create a slot and booking
    $slot = \App\Models\Slot::factory()->create([
        'doctor_id' => $doctor->id,
        'type' => 'online',
        'status' => 'booked',
    ]);

    $booking = \App\Models\Booking::factory()->create([
        'doctor_id' => $doctor->id,
        'slot_id' => $slot->id,
        'patient_name' => 'John Doe',
        'patient_email' => 'john@example.com',
        'patient_phone' => '+1234567890',
        'patient_notes' => 'Test patient notes',
        'status' => 'confirmed',
        'zoom_join_url' => 'https://zoom.us/j/123456789',
        'zoom_start_url' => 'https://zoom.us/s/123456789',
    ]);

    // Act as the doctor
    $this->actingAs($user);

    // Test that doctor can view booking details
    $response = $this->get("/doctor/bookings/{$booking->id}");
    $response->assertStatus(200);

    // Test that booking details are displayed
    $response->assertSee('John Doe');
    $response->assertSee('john@example.com');
    $response->assertSee('+1234567890');
    $response->assertSee('Test patient notes');
});

test('doctor cannot view other doctors bookings', function () {
    // Create two doctor users
    $user1 = User::factory()->create([
        'role' => 'doctor',
        'email' => 'doctor1@test.com',
    ]);

    $doctor1 = Doctor::factory()->create([
        'user_id' => $user1->id,
    ]);

    $user2 = User::factory()->create([
        'role' => 'doctor',
        'email' => 'doctor2@test.com',
    ]);

    $doctor2 = Doctor::factory()->create([
        'user_id' => $user2->id,
    ]);

    // Create a booking for doctor2
    $slot = \App\Models\Slot::factory()->create([
        'doctor_id' => $doctor2->id,
        'status' => 'booked',
    ]);

    $booking = \App\Models\Booking::factory()->create([
        'doctor_id' => $doctor2->id,
        'slot_id' => $slot->id,
        'patient_name' => 'Jane Smith',
        'status' => 'confirmed',
    ]);

    // Act as doctor1
    $this->actingAs($user1);

    // Test that doctor1 cannot view doctor2's booking
    $response = $this->get("/doctor/bookings/{$booking->id}");
    $response->assertStatus(404); // 404 instead of 403 for security (don't reveal resource exists)
});

test('doctor panel language switch sets locale', function () {
    $user = User::factory()->create([
        'role' => 'doctor',
        'email' => 'doctor-lang@test.com',
    ]);

    $doctor = Doctor::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    // Switch to Arabic
    $response = $this->get('/lang/ar');
    $response->assertRedirect();

    $this->assertSame('ar', session('locale'));
    $this->assertSame('ar', app()->getLocale());

    // Doctor panel still renders after locale switch
    $response = $this->get('/doctor');
    $response->assertStatus(200);
});
