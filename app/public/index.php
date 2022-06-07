<?php
require 'helpers.php';

echo "<h1>Export dbdiagram to migration</h1>";

$path = "../files/test.txt";
$myfile = fopen($path, "r") or die("Unable to open file!");
$matches = [];
$arr = [];

while(!feof($myfile)) {
    $buffer = trim(fgets($myfile));
    echo $buffer . "<br>";
    if ($buffer != '') {
        $arr[] = $buffer;
    }

    if ($buffer === '}') {
        $matches[] = $arr;
        $arr = [];
    }
}
fclose($myfile);

echo "<br> <b>Convert =============></b>";

for ($i = 0; $i < count($matches); $i++) {
    $helper = new Helpers;
    $helper->convertTable($matches[$i]);
}
