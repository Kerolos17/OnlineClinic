<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = \App\Models\Specialization::all();

        $doctors = [
            [
                'name_en' => 'Dr. Kerolos Morkos',
                'name_ar' => 'د.  كيرلس مرقص',
                'email' => 'keromorkus@gmail.com',
                'spec' => 1,
                'exp' => 15,
                'price' => 50,
            ],
            [
                'name_en' => 'Dr. Sarah Johnson',
                'name_ar' => 'د. سارة جونسون',
                'email' => 'sarah@wellclinic.com',
                'spec' => 2,
                'exp' => 10,
                'price' => 45,
            ],
            [
                'name_en' => 'Dr. Mohamed Ali',
                'name_ar' => 'د. محمد علي',
                'email' => 'mohamed@wellclinic.com',
                'spec' => 3,
                'exp' => 12,
                'price' => 40,
            ],
            [
                'name_en' => 'Dr. Emily Chen',
                'name_ar' => 'د. إيميلي تشين',
                'email' => 'emily@wellclinic.com',
                'spec' => 4,
                'exp' => 8,
                'price' => 55,
            ],
            [
                'name_en' => 'Dr. Omar Khalil',
                'name_ar' => 'د. عمر خليل',
                'email' => 'omar@wellclinic.com',
                'spec' => 5,
                'exp' => 20,
                'price' => 60,
            ],
        ];

        foreach ($doctors as $doc) {
            $user = \App\Models\User::create([
                'name_en' => $doc['name_en'],
                'name_ar' => $doc['name_ar'],
                'email' => $doc['email'],
                'password' => bcrypt('password'),
                'role' => 'doctor',
                'phone' => '+1234567890',
            ]);

            $spec = $specializations->find($doc['spec']);
            $doctor = \App\Models\Doctor::create([
                'user_id' => $user->id,
                'specialization_id' => $doc['spec'],
                'bio' => [
                    'en' => 'Experienced doctor with years of practice in '.$spec->name_en,
                    'ar' => 'طبيب ذو خبرة واسعة في مجال '.$spec->name_ar,
                ],
                'experience_years' => $doc['exp'],
                'languages' => ['en', 'ar'],
                'consultation_price' => $doc['price'],
                'rating' => rand(40, 50) / 10,
                'total_reviews' => rand(10, 100),
            ]);

            // Create slots for next 14 days
            $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            for ($i = 0; $i < 14; $i++) {
                $date = now()->addDays($i);
                $dayName = strtolower($date->format('l'));

                if (in_array($dayName, $days)) {
                    $startTime = strtotime('10:00');
                    $endTime = strtotime('20:00');

                    while ($startTime < $endTime) {
                        $slotStart = date('H:i:s', $startTime);
                        $slotEnd = date('H:i:s', $startTime + 1800); // 30 minutes

                        \App\Models\Slot::create([
                            'doctor_id' => $doctor->id,
                            'date' => $date->format('Y-m-d'),
                            'start_time' => $slotStart,
                            'end_time' => $slotEnd,
                            'status' => 'available',
                        ]);

                        $startTime += 1800;
                    }
                }
            }
        }
    }
}
