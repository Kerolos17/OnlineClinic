<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specialization>
 */
class SpecializationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specializations = [
            ['en' => 'Cardiology', 'ar' => 'أمراض القلب', 'icon' => '❤️'],
            ['en' => 'Dermatology', 'ar' => 'الأمراض الجلدية', 'icon' => '🧴'],
            ['en' => 'Neurology', 'ar' => 'الأمراض العصبية', 'icon' => '🧠'],
            ['en' => 'Pediatrics', 'ar' => 'طب الأطفال', 'icon' => '👶'],
            ['en' => 'Orthopedics', 'ar' => 'العظام', 'icon' => '🦴'],
            ['en' => 'Psychiatry', 'ar' => 'الطب النفسي', 'icon' => '🧘'],
            ['en' => 'General Medicine', 'ar' => 'الطب العام', 'icon' => '🩺'],
            ['en' => 'Gynecology', 'ar' => 'أمراض النساء', 'icon' => '👩‍⚕️'],
        ];

        $specialization = fake()->randomElement($specializations);

        return [
            'name_en'        => $specialization['en'],
            'name_ar'        => $specialization['ar'],
            'description_en' => fake()->sentence(10),
            'description_ar' => fake()->sentence(10),
            'icon'           => $specialization['icon'],
            'is_active'      => true,
        ];
    }

    /**
     * Indicate that the specialization is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
