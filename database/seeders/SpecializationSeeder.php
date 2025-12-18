<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = [
            [
                'name_en' => 'Cardiology',
                'name_ar' => 'أمراض القلب',
                'description_en' => 'Heart and cardiovascular system',
                'description_ar' => 'القلب والجهاز الدوري',
                'icon' => '❤️',
            ],
            [
                'name_en' => 'Dermatology',
                'name_ar' => 'الأمراض الجلدية',
                'description_en' => 'Skin, hair, and nails',
                'description_ar' => 'الجلد والشعر والأظافر',
                'icon' => '🧴',
            ],
            [
                'name_en' => 'Pediatrics',
                'name_ar' => 'طب الأطفال',
                'description_en' => 'Medical care for children',
                'description_ar' => 'الرعاية الطبية للأطفال',
                'icon' => '👶',
            ],
            [
                'name_en' => 'Psychiatry',
                'name_ar' => 'الطب النفسي',
                'description_en' => 'Mental health and disorders',
                'description_ar' => 'الصحة النفسية والاضطرابات',
                'icon' => '🧠',
            ],
            [
                'name_en' => 'Orthopedics',
                'name_ar' => 'جراحة العظام',
                'description_en' => 'Bones, joints, and muscles',
                'description_ar' => 'العظام والمفاصل والعضلات',
                'icon' => '🦴',
            ],
        ];

        foreach ($specializations as $spec) {
            \App\Models\Specialization::create($spec);
        }
    }
}
