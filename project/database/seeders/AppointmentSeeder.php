<?php

namespace Database\Seeders;

require_once("VitaGuardSeeder.php");

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->tableName = 'appointments';
        $this->runVitaGuardSeeder('appointments.csv');
    }
}
