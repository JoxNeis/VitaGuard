<?php

namespace Database\Seeders;

require_once("VitaGuardSeeder.php");

use Database\Seeders\VitaGuardSeeder;

class ArticleSeeder extends VitaGuardSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->tableName = 'articles';
        $this->runVitaGuardSeeder('articles.csv');
    }
}
