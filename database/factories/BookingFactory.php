<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id'       => \App\Models\Doctor::factory(),
            'slot_id'         => \App\Models\Slot::factory(),
            'patient_name'    => $this->faker->name(),
            'patient_email'   => $this->faker->safeEmail(),
            'patient_phone'   => $this->faker->phoneNumber(),
            'patient_notes'   => $this->faker->optional()->sentence(),
            'doctor_notes'    => $this->faker->optional()->sentence(),
            'status'          => $this->faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'amount'          => $this->faker->randomFloat(2, 50, 500),
            'zoom_meeting_id' => $this->faker->optional()->numerify('###########'),
            'zoom_join_url'   => $this->faker->optional()->url(),
            'zoom_start_url'  => $this->faker->optional()->url(),
            'zoom_created_at' => $this->faker->optional()->dateTimeThisMonth(),
            'appointment_at'  => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }
}
