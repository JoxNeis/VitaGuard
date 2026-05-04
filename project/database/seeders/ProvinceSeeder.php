<?php

namespace Database\Seeders;


require_once("VitaGuardSeeder.php");

use Database\Seeders\VitaGuardSeeder;


class ProvinceSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->tableName = 'provinces';
        $this->runVitaGuardSeeder('provinces.csv');
    }
}
