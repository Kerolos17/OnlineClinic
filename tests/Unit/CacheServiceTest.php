<?php

namespace Tests\Unit;

use App\Models\Doctor;
use App\Models\Slot;
use App\Models\Specialization;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache before each test
        Cache::flush();
    }

    /** @test */
    public function it_caches_specializations()
    {
        Specialization::create([
            'name_en' => 'Cardiology',
            'name_ar' => 'أمراض القلب',
            'icon' => '❤️',
            'is_active' => true,
        ]);

        // First call - should hit database
        $specializations = CacheService::getSpecializations();
        $this->assertCount(1, $specializations);

        // Second call - should hit cache
        $cachedSpecializations = CacheService::getSpecializations();
        $this->assertEquals($specializations, $cachedSpecializations);

        // Verify cache exists
        $this->assertTrue(Cache::has('specializations.active'));
    }

    /** @test */
    public function it_caches_doctors()
    {
        $this->createDoctor();

        // First call
        $doctors = CacheService::getDoctors();
        $this->assertCount(1, $doctors);

        // Verify cache exists
        $this->assertTrue(Cache::has('doctors.all'));
    }

    /** @test */
    public function it_caches_doctors_by_specialization()
    {
        $doctor = $this->createDoctor();

        $doctors = CacheService::getDoctors($doctor->specialization_id);
        $this->assertCount(1, $doctors);

        // Verify cache exists with specialization key
        $this->assertTrue(Cache::has("doctors.specialization.{$doctor->specialization_id}"));
    }

    /** @test */
    public function it_caches_single_doctor()
    {
        $doctor = $this->createDoctor();

        $cachedDoctor = CacheService::getDoctor($doctor->id);
        $this->assertEquals($doctor->id, $cachedDoctor->id);

        // Verify cache exists
        $this->assertTrue(Cache::has("doctor.{$doctor->id}"));
    }

    /** @test */
    public function it_caches_doctor_slots()
    {
        $doctor = $this->createDoctor();
        $date = now()->addDays(1)->format('Y-m-d');

        Slot::create([
            'doctor_id' => $doctor->id,
            'date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => 'available',
        ]);

        $slots = CacheService::getDoctorSlots($doctor->id, $date);
        $this->assertCount(1, $slots);

        // Verify cache exists
        $this->assertTrue(Cache::has("doctor.{$doctor->id}.slots.{$date}"));
    }

    /** @test */
    public function it_clears_doctor_cache()
    {
        $doctor = $this->createDoctor();

        // Cache doctor
        CacheService::getDoctor($doctor->id);
        $this->assertTrue(Cache::has("doctor.{$doctor->id}"));

        // Clear cache
        CacheService::clearDoctorCache($doctor->id);
        $this->assertFalse(Cache::has("doctor.{$doctor->id}"));
    }

    /** @test */
    public function it_clears_specialization_cache()
    {
        $specialization = Specialization::create([
            'name_en' => 'Cardiology',
            'name_ar' => 'أمراض القلب',
            'icon' => '❤️',
            'is_active' => true,
        ]);

        // Cache specialization
        CacheService::getSpecialization($specialization->id);
        $this->assertTrue(Cache::has("specialization.{$specialization->id}"));

        // Clear cache
        CacheService::clearSpecializationCache($specialization->id);
        $this->assertFalse(Cache::has("specialization.{$specialization->id}"));
    }

    /** @test */
    public function it_clears_slot_cache()
    {
        $doctor = $this->createDoctor();
        $date = now()->addDays(1)->format('Y-m-d');

        Slot::create([
            'doctor_id' => $doctor->id,
            'date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => 'available',
        ]);

        // Cache slots
        CacheService::getDoctorSlots($doctor->id, $date);
        $this->assertTrue(Cache::has("doctor.{$doctor->id}.slots.{$date}"));

        // Clear cache
        CacheService::clearSlotCache($doctor->id, $date);
        $this->assertFalse(Cache::has("doctor.{$doctor->id}.slots.{$date}"));
    }

    /** @test */
    public function it_clears_all_cache()
    {
        $this->createDoctor();
        CacheService::getDoctors();
        CacheService::getSpecializations();

        $this->assertTrue(Cache::has('doctors.all'));
        $this->assertTrue(Cache::has('specializations.active'));

        CacheService::clearAll();

        $this->assertFalse(Cache::has('doctors.all'));
        $this->assertFalse(Cache::has('specializations.active'));
    }

    /** @test */
    public function it_warms_up_cache()
    {
        $this->createDoctor();

        CacheService::warmUp();

        $this->assertTrue(Cache::has('specializations.active'));
        $this->assertTrue(Cache::has('doctors.all'));
    }

    protected function createDoctor()
    {
        $specialization = Specialization::create([
            'name_en' => 'Cardiology',
            'name_ar' => 'أمراض القلب',
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

        return Doctor::create([
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
    }
}
