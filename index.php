<?php
require 'vendor/autoload.php';

use App\InputFile;
use App\Convert;
use App\OutputFile;

try {
    $filePath = "export-migration.txt";
    $inputFile = new InputFile($filePath);
    $tables = $inputFile->convertFileToTables();

    $convert = new Convert($tables);
    $convertedTables = $convert->getTable();

    foreach ($convertedTables as $key => $value) {
        $outputFile = new OutputFile();
        $filename = "database/migrations/" . date("Y_m_d") . "_" . time() . "_create_" . $key . "_table.php";

        $migrationFiles = fopen($filename, "w");
        fwrite($migrationFiles , $outputFile->intergrateFile($key, $value));
        fclose($migrationFiles);
    }
} catch(Exception $e) {
    exit($e->getMessage());
}

echo "\033[;32mSuccessfuly convert\033[0m\n";
