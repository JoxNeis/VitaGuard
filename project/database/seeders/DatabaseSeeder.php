<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            ProvinceSeeder::class,
            CitySeeder::class,
            DistrictSeeder::class,
            ArticleTopicSeeder::class,
            UserSeeder::class,
            ArticleSeeder::class,
            MemberSeeder::class,
            MedicalHistorySeeder::class,
            AllergenSeeder::class,
            MemberAllergiesSeeder::class,
            MemberMedicalProfileSeeder::class,
            DoctorSeeder::class,
            SpecialtiesSeeder::class,
            DoctorSpecialtiesSeeder::class,
            FacilitySeeder::class,
            FacilityHoursSeeder::class,
            ScheduleSeeder::class,
            FacilityAdminSeeder::class,
            AppointmentSeeder::class,
            MedicineSeeder::class,
            OnlineSessionSeeder::class,
            ConsultationSeeder::class,
            ChatSeeder::class,
            PrescriptionSeeder::class,            
            PrescriptionDetailSeeder::class,
        ]);
    }
}
