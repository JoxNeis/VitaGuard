<?php

namespace Database\Seeders;

require_once("VitaGuardSeeder.php");

use Database\Seeders\VitaGuardSeeder;

class MemberAllergiesSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->tableName = 'member_allergies';
        $this->runVitaGuardSeeder('member_allergies.csv');
    }
}
