<?php
require 'vendor/autoload.php';

use App\InputFile;
use App\Convert;

try {
    $filePath = "export-migration.txt";
    $inputFile = new InputFile($filePath);
    $tables = $inputFile->convertFileToTables();

    $helper = new Convert($tables);
    $convertedTables = $helper->getTable();

    echo "<pre>";
    print_r($convertedTables);
    echo "</pre>";

    // foreach ($convertedTables as $key => $value) {
    //     $filename = "database/migrations/" . date("Y_m_d") . "_" . time() . "_create_" . $key . "_table.php";
    //     $myfile = fopen($filename, "w");
    //     fwrite($myfile, intergrateFile($key, $value));
    // }
} catch(Exception $e) {
    exit($e->getMessage());
}


function intergrateFile($key, $value)
{
    $str = "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {\r\n";
    $str .= $value;
    $str .= "\r\n    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('" . $key . "');
    }
};";
    return $str;
}
echo "\033[;32mPham Xuan Tung <3 <3\033[0m\n";
