<?php

namespace Database\Seeders;

require_once("VitaGuardSeeder.php");

use Database\Seeders\VitaGuardSeeder;

class MemberSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->tableName = 'members';
        $this->runVitaGuardSeeder('members.csv');
    }
}
