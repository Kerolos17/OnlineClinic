<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name_en' => 'Admin',
            'name_ar' => 'المسؤول',
            'email' => 'admin@wellclinic.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->call([
            SpecializationSeeder::class,
            DoctorSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
