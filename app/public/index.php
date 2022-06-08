<?php
require 'convert.php';

$path = "export-migration.txt";
$myfile = fopen($path, "r") or die("Unable to open file!");
$matches = [];
$arr = [];

/**
 * Reading lines in file
 */
while(!feof($myfile)) {
    $buffer = trim(fgets($myfile));
    if ($buffer != '') {
        $arr[] = $buffer;
    }

    if ($buffer === '}') {
        $matches[] = $arr;
        $arr = [];
    }
}
fclose($myfile);

$helper = new Convert($matches);

/**
 * Export files
 */
$convertedTables = $helper->getTable();

foreach ($convertedTables as $key => $value) {
    $filename = "database/migrations/" . date("Y_m_d") . "_" . time() . "_create_" . $key . "_table.php";
    $myfile = fopen($filename, "w");
    fwrite($myfile, intergrateFile($key, $value));
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
