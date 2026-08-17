<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DoctorListingTest extends TestCase
{
    use RefreshDatabase;

    protected $specialization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->specialization = Specialization::create([
            'name_en' => 'Cardiology',
            'name_ar' => 'أمراض القلب',
            'description_en' => 'Heart specialist',
            'description_ar' => 'متخصص في أمراض القلب',
            'icon' => '❤️',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function user_can_view_doctors_list()
    {
        $response = $this->get(route('doctors.index'));

        $response->assertStatus(200);
        $response->assertViewIs('doctors.index');
    }

    #[Test]
    public function doctors_list_shows_active_doctors_only()
    {
        // Create active doctor
        $activeDoctor = $this->createDoctor('Dr. Active', true);

        // Create inactive doctor
        $inactiveDoctor = $this->createDoctor('Dr. Inactive', false);

        $response = $this->get(route('doctors.index'));

        $response->assertSee('Dr. Active');
        $response->assertDontSee('Dr. Inactive');
    }

    #[Test]
    public function user_can_filter_doctors_by_specialization()
    {
        $cardiology = $this->specialization;

        $dermatology = Specialization::create([
            'name_en' => 'Dermatology',
            'name_ar' => 'الأمراض الجلدية',
            'icon' => '🩺',
            'is_active' => true,
        ]);

        $cardiologist = $this->createDoctor('Dr. Heart', true, $cardiology->id);
        $dermatologist = $this->createDoctor('Dr. Skin', true, $dermatology->id);

        $response = $this->get(route('doctors.index', ['specialization' => $cardiology->id]));

        $response->assertSee('Dr. Heart');
        $response->assertDontSee('Dr. Skin');
    }

    #[Test]
    public function user_can_view_doctor_profile()
    {
        $doctor = $this->createDoctor('Dr. John Doe', true);

        $response = $this->get(route('doctors.show', $doctor->id));

        $response->assertStatus(200);
        $response->assertViewIs('doctors.show');
        $response->assertSee('Dr. John Doe');
    }

    #[Test]
    public function doctor_profile_shows_specialization()
    {
        $doctor = $this->createDoctor('Dr. John Doe', true);

        $response = $this->get(route('doctors.show', $doctor->id));

        $response->assertSee('Cardiology');
    }

    #[Test]
    public function doctor_profile_shows_rating()
    {
        $doctor = $this->createDoctor('Dr. John Doe', true);

        $response = $this->get(route('doctors.show', $doctor->id));

        $response->assertSee('4.5');
    }

    #[Test]
    public function doctor_profile_shows_consultation_price()
    {
        $doctor = $this->createDoctor('Dr. John Doe', true);

        $response = $this->get(route('doctors.show', $doctor->id));

        $response->assertSee('$100');
    }

    #[Test]
    public function doctors_are_sorted_by_rating()
    {
        $doctor1 = $this->createDoctor('Dr. Low Rating', true);
        $doctor1->update(['rating' => 3.0]);

        $doctor2 = $this->createDoctor('Dr. High Rating', true);
        $doctor2->update(['rating' => 5.0]);

        $response = $this->get(route('doctors.index'));

        // High rating doctor should appear first
        $content = $response->getContent();
        $pos1 = strpos($content, 'Dr. High Rating');
        $pos2 = strpos($content, 'Dr. Low Rating');

        $this->assertLessThan($pos2, $pos1);
    }

    #[Test]
    public function inactive_doctor_profile_returns_404()
    {
        $doctor = $this->createDoctor('Dr. Inactive', false);

        $response = $this->get(route('doctors.show', $doctor->id));

        $response->assertStatus(404);
    }

    protected function createDoctor($name, $isActive = true, $specializationId = null)
    {
        $user = User::create([
            'name_en' => $name,
            'name_ar' => $name,
            'email' => strtolower(str_replace(' ', '', $name)).'@example.com',
            'password' => bcrypt('password'),
            'role' => 'doctor',
        ]);

        return Doctor::create([
            'user_id' => $user->id,
            'specialization_id' => $specializationId ?? $this->specialization->id,
            'bio' => ['en' => 'Experienced doctor', 'ar' => 'طبيب ذو خبرة'],
            'experience_years' => 10,
            'languages' => ['English', 'Arabic'],
            'consultation_price' => 100.00,
            'rating' => 4.5,
            'total_reviews' => 50,
            'is_active' => $isActive,
        ]);
    }
}
