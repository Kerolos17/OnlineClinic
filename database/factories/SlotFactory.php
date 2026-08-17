<?php
namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Slot>
 */
class SlotFactory extends Factory
{
    protected $model = Slot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->time('H:i:s', '18:00:00');
        $endTime   = date('H:i:s', strtotime($startTime) + 1800); // 30 minutes later

        return [
            'doctor_id'       => Doctor::factory(),
            'date'            => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'start_time'      => $startTime,
            'end_time'        => $endTime,
            'status'          => $this->faker->randomElement(['available', 'booked', 'blocked']),
            'type'            => $this->faker->randomElement(['online', 'clinic']),
            'notes'           => $this->faker->optional()->sentence(),
            'zoom_meeting_id' => $this->faker->optional()->numerify('###########'),
            'zoom_join_url'   => $this->faker->optional()->url(),
            'zoom_start_url'  => $this->faker->optional()->url(),
        ];
    }

    /**
     * Indicate that the slot is available.
     */
    public function available(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'available',
        ]);
    }

    /**
     * Indicate that the slot is booked.
     */
    public function booked(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'booked',
        ]);
    }

    /**
     * Indicate that the slot is for online consultation.
     */
    public function online(): static
    {
        return $this->state(fn(array $attributes) => [
            'type'            => 'online',
            'zoom_meeting_id' => $this->faker->numerify('###########'),
            'zoom_join_url'   => $this->faker->url(),
            'zoom_start_url'  => $this->faker->url(),
            'notes'           => null,
        ]);
    }

    /**
     * Indicate that the slot is for clinic visit.
     */
    public function clinic(): static
    {
        return $this->state(fn(array $attributes) => [
            'type'            => 'clinic',
            'zoom_meeting_id' => null,
            'zoom_join_url'   => null,
            'zoom_start_url'  => null,
            'notes'           => $this->faker->optional()->sentence(),
        ]);
    }

    /**
     * Indicate that the slot is in the future.
     */
    public function future(): static
    {
        return $this->state(fn(array $attributes) => [
            'date' => $this->faker->dateTimeBetween('tomorrow', '+3 months')->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the slot is today.
     */
    public function today(): static
    {
        return $this->state(fn(array $attributes) => [
            'date' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the slot is in the past.
     */
    public function past(): static
    {
        return $this->state(fn(array $attributes) => [
            'date' => $this->faker->dateTimeBetween('-1 month', 'yesterday')->format('Y-m-d'),
        ]);
    }
}
