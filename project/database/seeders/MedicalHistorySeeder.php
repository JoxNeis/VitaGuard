<?php

namespace Database\Seeders;

require_once("VitaGuardSeeder.php");

use Database\Seeders\VitaGuardSeeder;

class MedicalHistorySeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->tableName = 'medical_histories';
        $this->runVitaGuardSeeder('medical_histories.csv');
    }
}
