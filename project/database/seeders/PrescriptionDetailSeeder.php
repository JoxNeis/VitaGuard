<?php

namespace Database\Seeders;

require_once("VitaGuardSeeder.php");

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrescriptionDetailSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->tableName = 'prescription_details';
        $this->runVitaGuardSeeder('prescription_details.csv');
    }
}
