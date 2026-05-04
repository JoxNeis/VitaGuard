<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class VitaGuardSeeder extends Seeder
{
    protected array $capsule = [];
    protected string $tableName = "";

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

    }

    /**
     * Run the database seeds with CSV as init data.
     */
    protected function runVitaGuardSeeder(string $filePath): void
    {
        if (empty($this->tableName)) {
            throw new Exception("Table name is not set.");
        }

        $csv = $this->openCSV($filePath);

        $this->createCapsule($csv);

        $this->csvToDatabase($csv);

        fclose($csv);
    }

    /**
     * Open CSV file
     * @param string $filePath
     * @throws Exception
     * @return bool|resource
     */
    protected function openCSV(string $filePath)
    {
        // $fullPath = storage_path($filePath);
        $fullPath = database_path('seeders/values/'.$filePath);

        if (!file_exists($fullPath)) {
            throw new Exception(
                "CSV file not found: {$fullPath}"
            );
        }

        $csv = fopen($fullPath, 'r');

        if ($csv === false) {
            throw new Exception(
                "Unable to open CSV file: {$fullPath}"
            );
        }

        return $csv;
    }

    /**
     * Store header names
     * @param array $row
     * @return void
     */
    protected function createCapsule($csv): void
    {
        $header = fgetcsv($csv);

        if ($header === false) {
            throw new Exception(
                "CSV header row missing."
            );
        }

        $this->capsule = $header;
    }

    /**
     * Read CSV file
     * @param mixed $csv
     * @return void
     */
    protected function csvToDatabase($csv): void
    {
        $batch = [];

        while (($row = fgetcsv($csv)) !== false) {

            $dataArray = $this->createDataArray($row);

            $dataArray = $this->modifyData($dataArray);

            $batch[] = $dataArray;
        }

        if (!empty($batch)) {
            DB::table($this->tableName)
                ->insert($batch);
        }
    }

    /**
     * Change CSV rows to array with the right column as array key
     * @param array $row
     * @return array
     */
    protected function createDataArray(array $row): array
    {
        if (count($row) !== count($this->capsule)) {
            throw new Exception(
                "CSV column count mismatch."
            );
        }

        return array_combine(
            $this->capsule,
            $row
        );
    }

    /**
     * To be overridden if data manipulation needed
     * @param mixed $dataArray
     * @return array
     */
    protected function modifyData($dataArray): array
    {
        $timeFields = [
            'open_time',
            'close_time',
            'break_start_time',
            'break_end_time'
        ];

        foreach ($timeFields as $field) {
            if (isset($dataArray[$field]) && trim($dataArray[$field]) === '') {
                $dataArray[$field] = null;
            }
        }

        return $dataArray;
    }

}