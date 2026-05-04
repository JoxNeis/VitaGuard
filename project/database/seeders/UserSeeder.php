<?php

namespace Database\Seeders;

require_once("VitaGuardSeeder.php");

use Database\Seeders\VitaGuardSeeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->tableName = 'users';
        $this->runVitaGuardSeeder('users.csv');
    }

    protected function modifyData($dataArray): array
    {
        if (isset($dataArray['password'])) {

            $dataArray['password_hashed'] =
                Hash::make($dataArray['password']);

            unset($dataArray['password']);
        }
        return $dataArray;
    }

}
