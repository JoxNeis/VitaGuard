<?php

namespace Database\Seeders;

require_once("VitaGuardSeeder.php");

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->tableName = 'schedules';
        $this->runVitaGuardSeeder('schedules.csv');
    }
}
