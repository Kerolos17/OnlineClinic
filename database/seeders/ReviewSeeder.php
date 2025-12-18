<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = Doctor::all();

        $reviews = [
            [
                'names' => ['John Smith', 'محمد أحمد', 'Sarah Johnson', 'فاطمة علي'],
                'comments' => [
                    'Excellent doctor! Very professional and caring.',
                    'طبيب ممتاز! محترف جداً ومهتم بالمريض.',
                    'Great experience, highly recommend!',
                    'تجربة رائعة، أنصح بشدة!',
                ],
            ],
        ];

        foreach ($doctors as $doctor) {
            // Generate 5-15 reviews per doctor
            $reviewCount = rand(5, 15);

            for ($i = 0; $i < $reviewCount; $i++) {
                $rating = rand(3, 5); // Most reviews are positive

                Review::create([
                    'doctor_id' => $doctor->id,
                    'patient_name' => $reviews[0]['names'][array_rand($reviews[0]['names'])],
                    'patient_email' => 'patient'.rand(1, 1000).'@example.com',
                    'rating' => $rating,
                    'comment' => $reviews[0]['comments'][array_rand($reviews[0]['comments'])],
                    'status' => 'approved', // Auto-approve for demo
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);
            }

            // Update doctor's rating and review count
            $approvedReviews = $doctor->approvedReviews;
            $doctor->update([
                'rating' => round($approvedReviews->avg('rating'), 1),
                'total_reviews' => $approvedReviews->count(),
            ]);
        }
    }
}
