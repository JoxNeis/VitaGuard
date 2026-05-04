<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

require_once("VitaGuardSeeder.php");
class FacilityHoursSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $this->tableName = 'facility_hours';
        $this->runVitaGuardSeeder('facility_hours.csv');
    }
}
