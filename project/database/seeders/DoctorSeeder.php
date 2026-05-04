<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

require_once("VitaGuardSeeder.php");

class DoctorSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $this->tableName = 'doctors';
        $this->runVitaGuardSeeder('doctors.csv');
    }
}
