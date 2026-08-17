<?php

use App\Models\Doctor;
use App\Models\Slot;
use App\Models\User;
use App\Services\DoctorSlotService;
use App\Services\PatientNotificationService;
use App\Services\ZoomService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a doctor user for testing
    $this->user = User::factory()->create([
        'role' => 'doctor',
    ]);

    $this->doctor = Doctor::factory()->create([
        'user_id' => $this->user->id,
    ]);
});

it('can update appointment slot successfully', function () {
    // Mock the services
    $this->mock(ZoomService::class);
    $this->mock(PatientNotificationService::class);

    // Create a slot
    $slot = Slot::factory()->create([
        'doctor_id'  => $this->doctor->id,
        'date'       => now()->addDays(2)->format('Y-m-d'),
        'start_time' => '09:00',
        'end_time'   => '09:30',
        'type'       => 'clinic',
        'status'     => 'available',
    ]);

    $service = app(DoctorSlotService::class);

    $updateData = [
        'date'       => now()->addDays(3)->format('Y-m-d'),
        'start_time' => '10:00',
        'end_time'   => '10:30',
        'type'       => 'online',
        'notes'      => 'Updated appointment',
    ];

    $updatedSlot = $service->updateSlot($slot, $updateData);

    expect($updatedSlot->date->format('Y-m-d'))->toBe($updateData['date']);
    expect($updatedSlot->start_time)->toBe($updateData['start_time']);
    expect($updatedSlot->end_time)->toBe($updateData['end_time']);
    expect($updatedSlot->type)->toBe($updateData['type']);
    expect($updatedSlot->notes)->toBe($updateData['notes']);
});

it('prevents deletion of appointments within 24 hours with bookings', function () {
    // Mock the services
    $this->mock(ZoomService::class);
    $this->mock(PatientNotificationService::class);

    // Create a slot for tomorrow with a booking
    $slot = Slot::factory()->create([
        'doctor_id'  => $this->doctor->id,
        'date'       => now()->addDay()->format('Y-m-d'),
        'start_time' => '09:00',
        'end_time'   => '09:30',
        'type'       => 'online',
        'status'     => 'booked',
    ]);

    // Create a booking for this slot
    $booking = \App\Models\Booking::factory()->create([
        'slot_id'       => $slot->id,
        'patient_name'  => 'Test Patient',
        'patient_email' => 'test@example.com',
        'patient_phone' => '1234567890',
        'status'        => 'confirmed',
    ]);

    $service = app(DoctorSlotService::class);

    expect(fn() => $service->deleteSlot($slot))
        ->toThrow(\Illuminate\Validation\ValidationException::class);
});

it('allows deletion of appointments more than 24 hours away', function () {
    // Mock the services
    $this->mock(ZoomService::class);
    $notificationService = $this->mock(PatientNotificationService::class);
    $notificationService->shouldReceive('notifyAppointmentCancellation')
        ->once()
        ->with(\Mockery::type(\App\Models\Slot::class));

    // Create a slot for next week with a booking
    $slot = Slot::factory()->create([
        'doctor_id'  => $this->doctor->id,
        'date'       => now()->addWeek()->format('Y-m-d'),
        'start_time' => '09:00',
        'end_time'   => '09:30',
        'type'       => 'online',
        'status'     => 'booked',
    ]);

    // Create a booking for this slot
    $booking = \App\Models\Booking::factory()->create([
        'slot_id'       => $slot->id,
        'patient_name'  => 'Test Patient',
        'patient_email' => 'test@example.com',
        'patient_phone' => '1234567890',
        'status'        => 'confirmed',
    ]);

    $service = app(DoctorSlotService::class);

    $result = $service->deleteSlot($slot);

    expect($result)->toBeTrue();
    expect(Slot::find($slot->id))->toBeNull();
});

it('can delete available appointments without restrictions', function () {
    // Mock the services
    $this->mock(ZoomService::class);
    $this->mock(PatientNotificationService::class);

    // Create an available slot
    $slot = Slot::factory()->create([
        'doctor_id'  => $this->doctor->id,
        'date'       => now()->addDay()->format('Y-m-d'),
        'start_time' => '09:00',
        'end_time'   => '09:30',
        'type'       => 'clinic',
        'status'     => 'available',
    ]);

    $service = app(DoctorSlotService::class);

    $result = $service->deleteSlot($slot);

    expect($result)->toBeTrue();
    expect(Slot::find($slot->id))->toBeNull();
});
