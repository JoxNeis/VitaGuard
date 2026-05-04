<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
require_once("VitaGuardSeeder.php");

class DoctorSpecialtiesSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $this->tableName = 'doctor_specialties';
        $this->runVitaGuardSeeder('doctor_specialties.csv');
    }
}
