<?php

namespace Database\Seeders;

require_once("VitaGuardSeeder.php");

use Database\Seeders\VitaGuardSeeder;

class MemberMedicalProfileSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->tableName = 'member_medical_profiles';
        $this->runVitaGuardSeeder('member_medical_profiles.csv');
    }
}
