<?php

    namespace Database\Seeders;

    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;

    require_once("VitaGuardSeeder.php");
    
    class SpecialtiesSeeder extends VitaGuardSeeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            //
            $this->tableName = 'specialties';
            $this->runVitaGuardSeeder('specialties.csv');
        }
    }

?>
