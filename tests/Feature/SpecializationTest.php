<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SpecializationTest extends TestCase
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
    public function user_can_view_specializations_list()
    {
        $response = $this->get(route('specializations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('specializations.index');
        $response->assertSee('Cardiology');
    }

    #[Test]
    public function user_can_view_specialization_details()
    {
        $doctor = Doctor::factory()->create([
            'specialization_id' => $this->specialization->id,
            'is_active' => true,
        ]);

        $response = $this->get(route('specializations.show', $this->specialization->id));

        $response->assertStatus(200);
        $response->assertViewIs('specializations.show');
        $response->assertSee('Cardiology');
        $response->assertSee($doctor->user->name_en);
    }

    #[Test]
    public function inactive_specialization_returns_404()
    {
        $this->specialization->update(['is_active' => false]);

        $this->get(route('specializations.show', $this->specialization->id))
            ->assertStatus(404);
    }

    #[Test]
    public function non_existent_specialization_returns_404()
    {
        $this->get(route('specializations.show', 999))
            ->assertStatus(404);
    }
}