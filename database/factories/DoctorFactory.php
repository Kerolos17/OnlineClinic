<?php
namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'user_id'            => User::factory(),
            'specialization_id'  => Specialization::factory(),
            'bio'                => [
                'en' => $this->faker->paragraph(),
                'ar' => 'سيرة ذاتية للطبيب',
            ],
            'experience_years'   => $this->faker->numberBetween(1, 30),
            'languages'          => ['en', 'ar'],
            'consultation_price' => $this->faker->randomFloat(2, 50, 500),
            'rating'             => $this->faker->randomFloat(2, 3.0, 5.0),
            'total_reviews'      => $this->faker->numberBetween(0, 100),
            'is_active'          => true,
        ];
    }
}
