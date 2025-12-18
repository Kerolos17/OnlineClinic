<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Slot;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $doctor;

    protected $slot;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $specialization = Specialization::create([
            'name_en' => 'Cardiology',
            'name_ar' => 'أمراض القلب',
            'description_en' => 'Heart specialist',
            'description_ar' => 'متخصص في أمراض القلب',
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

        $this->slot = Slot::create([
            'doctor_id' => $this->doctor->id,
            'date' => now()->addDays(1),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => 'available',
        ]);
    }

    /** @test */
    public function user_can_view_booking_form()
    {
        $response = $this->get(route('booking.create', [
            'doctor' => $this->doctor->id,
            'slot' => $this->slot->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('booking.create');
        $response->assertViewHas('doctor');
        $response->assertViewHas('slot');
    }

    /** @test */
    public function user_can_create_booking_with_valid_data()
    {
        $bookingData = [
            'doctor_id' => $this->doctor->id,
            'slot_id' => $this->slot->id,
            'patient_name' => 'John Patient',
            'patient_email' => 'patient@example.com',
            'patient_phone' => '+1234567890',
            'patient_notes' => 'I have chest pain',
        ];

        $response = $this->post(route('booking.store'), $bookingData);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'doctor_id' => $this->doctor->id,
            'patient_email' => 'patient@example.com',
        ]);

        // Check slot is now booked
        $this->slot->refresh();
        $this->assertEquals('booked', $this->slot->status);
    }

    /** @test */
    public function booking_requires_patient_name()
    {
        $bookingData = [
            'doctor_id' => $this->doctor->id,
            'slot_id' => $this->slot->id,
            'patient_email' => 'patient@example.com',
            'patient_phone' => '+1234567890',
        ];

        $response = $this->post(route('booking.store'), $bookingData);

        $response->assertSessionHasErrors('patient_name');
    }

    /** @test */
    public function booking_requires_valid_email()
    {
        $bookingData = [
            'doctor_id' => $this->doctor->id,
            'slot_id' => $this->slot->id,
            'patient_name' => 'John Patient',
            'patient_email' => 'invalid-email',
            'patient_phone' => '+1234567890',
        ];

        $response = $this->post(route('booking.store'), $bookingData);

        $response->assertSessionHasErrors('patient_email');
    }

    /** @test */
    public function booking_requires_phone_number()
    {
        $bookingData = [
            'doctor_id' => $this->doctor->id,
            'slot_id' => $this->slot->id,
            'patient_name' => 'John Patient',
            'patient_email' => 'patient@example.com',
        ];

        $response = $this->post(route('booking.store'), $bookingData);

        $response->assertSessionHasErrors('patient_phone');
    }

    /** @test */
    public function booking_creates_payment_record()
    {
        $bookingData = [
            'doctor_id' => $this->doctor->id,
            'slot_id' => $this->slot->id,
            'patient_name' => 'John Patient',
            'patient_email' => 'patient@example.com',
            'patient_phone' => '+1234567890',
        ];

        $this->post(route('booking.store'), $bookingData);

        $booking = Booking::where('patient_email', 'patient@example.com')->first();

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => $this->doctor->consultation_price,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function user_can_view_booking_success_page()
    {
        $booking = Booking::create([
            'doctor_id' => $this->doctor->id,
            'slot_id' => $this->slot->id,
            'patient_name' => 'John Patient',
            'patient_email' => 'patient@example.com',
            'patient_phone' => '+1234567890',
            'status' => 'confirmed',
            'amount' => 100.00,
            'appointment_at' => now()->addDays(1),
        ]);

        $response = $this->get(route('booking.success', $booking->id));

        $response->assertStatus(200);
        $response->assertViewIs('booking.success');
        $response->assertViewHas('booking');
        $response->assertSee('John Patient');
    }

    /** @test */
    public function cannot_book_unavailable_slot()
    {
        $this->slot->update(['status' => 'booked']);

        $bookingData = [
            'doctor_id' => $this->doctor->id,
            'slot_id' => $this->slot->id,
            'patient_name' => 'John Patient',
            'patient_email' => 'patient@example.com',
            'patient_phone' => '+1234567890',
        ];

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->post(route('booking.store'), $bookingData);
    }

    /** @test */
    public function patient_notes_are_optional()
    {
        $bookingData = [
            'doctor_id' => $this->doctor->id,
            'slot_id' => $this->slot->id,
            'patient_name' => 'John Patient',
            'patient_email' => 'patient@example.com',
            'patient_phone' => '+1234567890',
        ];

        $response = $this->post(route('booking.store'), $bookingData);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'patient_email' => 'patient@example.com',
            'patient_notes' => null,
        ]);
    }

    /** @test */
    public function patient_name_must_be_at_least_3_characters()
    {
        $bookingData = [
            'doctor_id' => $this->doctor->id,
            'slot_id' => $this->slot->id,
            'patient_name' => 'Jo',
            'patient_email' => 'patient@example.com',
            'patient_phone' => '+1234567890',
        ];

        $response = $this->post(route('booking.store'), $bookingData);

        $response->assertSessionHasErrors('patient_name');
    }

    /** @test */
    public function phone_number_must_be_at_least_10_characters()
    {
        $bookingData = [
            'doctor_id' => $this->doctor->id,
            'slot_id' => $this->slot->id,
            'patient_name' => 'John Patient',
            'patient_email' => 'patient@example.com',
            'patient_phone' => '123',
        ];

        $response = $this->post(route('booking.store'), $bookingData);

        $response->assertSessionHasErrors('patient_phone');
    }
}
