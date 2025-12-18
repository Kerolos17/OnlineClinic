<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;
use App\Services\CacheService;
use App\Services\PerformanceMonitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function doctors_list_uses_caching()
    {
        $this->createDoctors(5);

        // Clear cache
        Cache::flush();

        // First request - should cache
        $response1 = $this->get(route('doctors.index'));
        $response1->assertStatus(200);

        // Check cache exists
        $this->assertTrue(Cache::has('doctors.all'));

        // Second request - should use cache
        DB::enableQueryLog();
        $response2 = $this->get(route('doctors.index'));
        $queryCount = count(DB::getQueryLog());

        // Should have minimal queries (just for layout, not doctors)
        $this->assertLessThan(5, $queryCount);
    }

    /** @test */
    public function doctor_profile_uses_caching()
    {
        $doctor = $this->createDoctors(1)->first();

        // Clear cache
        Cache::flush();

        // First request
        $this->get(route('doctors.show', $doctor->id));

        // Check cache exists
        $this->assertTrue(Cache::has("doctor.{$doctor->id}"));

        // Second request should use cache
        DB::enableQueryLog();
        $this->get(route('doctors.show', $doctor->id));
        $queryCount = count(DB::getQueryLog());

        $this->assertLessThan(5, $queryCount);
    }

    /** @test */
    public function doctors_list_has_no_n_plus_one_queries()
    {
        $this->createDoctors(10);

        PerformanceMonitor::start();

        $response = $this->get(route('doctors.index'));

        $stats = PerformanceMonitor::stop();

        // Should have less than 10 queries even with 10 doctors
        $this->assertLessThan(10, $stats['query_count']);
    }

    /** @test */
    public function doctor_profile_has_no_n_plus_one_queries()
    {
        $doctor = $this->createDoctors(1)->first();

        PerformanceMonitor::start();

        $response = $this->get(route('doctors.show', $doctor->id));

        $stats = PerformanceMonitor::stop();

        // Should have minimal queries with eager loading
        $this->assertLessThan(8, $stats['query_count']);
    }

    /** @test */
    public function cache_is_cleared_when_doctor_is_updated()
    {
        $doctor = $this->createDoctors(1)->first();

        // Cache doctor
        CacheService::getDoctor($doctor->id);
        $this->assertTrue(Cache::has("doctor.{$doctor->id}"));

        // Update doctor
        $doctor->update(['rating' => 5.0]);

        // Cache should be cleared
        $this->assertFalse(Cache::has("doctor.{$doctor->id}"));
    }

    /** @test */
    public function cache_is_cleared_when_specialization_is_updated()
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

        // Update specialization
        $specialization->update(['name_en' => 'Updated Cardiology']);

        // Cache should be cleared
        $this->assertFalse(Cache::has("specialization.{$specialization->id}"));
    }

    /** @test */
    public function page_load_time_is_acceptable()
    {
        $this->createDoctors(20);

        $start = microtime(true);
        $response = $this->get(route('doctors.index'));
        $time = (microtime(true) - $start) * 1000;

        $response->assertStatus(200);

        // Page should load in less than 1 second
        $this->assertLessThan(1000, $time);
    }

    /** @test */
    public function memory_usage_is_acceptable()
    {
        $this->createDoctors(50);

        $memoryBefore = memory_get_usage(true);

        $response = $this->get(route('doctors.index'));

        $memoryAfter = memory_get_usage(true);
        $memoryUsed = $memoryAfter - $memoryBefore;

        $response->assertStatus(200);

        // Should use less than 10MB for 50 doctors
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsed);
    }

    protected function createDoctors($count)
    {
        $specialization = Specialization::create([
            'name_en' => 'Cardiology',
            'name_ar' => 'أمراض القلب',
            'icon' => '❤️',
            'is_active' => true,
        ]);

        $doctors = collect();

        for ($i = 1; $i <= $count; $i++) {
            $user = User::create([
                'name_en' => "Dr. Test {$i}",
                'name_ar' => "د. تست {$i}",
                'email' => "doctor{$i}@example.com",
                'password' => bcrypt('password'),
                'role' => 'doctor',
            ]);

            $doctors->push(Doctor::create([
                'user_id' => $user->id,
                'specialization_id' => $specialization->id,
                'bio' => ['en' => 'Experienced doctor', 'ar' => 'طبيب ذو خبرة'],
                'experience_years' => 10,
                'languages' => ['English', 'Arabic'],
                'consultation_price' => 100.00,
                'rating' => 4.5,
                'total_reviews' => 50,
                'is_active' => true,
            ]));
        }

        return $doctors;
    }
}
