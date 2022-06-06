<?php
$path = "../files/test.txt";
$myfile = fopen($path, "r") or die("Unable to open file!");
$i = 0;
$matches = array();

while(!feof($myfile)) {
    $buffer = fgets($myfile);
    echo $buffer. "<br>";
    $matches[] = trim($buffer);
}
fclose($myfile);

echo "<br> <b>Convert =============></b> <br><br>";

echo convertTableName($matches[0]) . "<br>";
for ($i = 1; $i < count($matches) - 1; $i++) {
    echo " &nbsp; &nbsp;" . convertColumn($matches[$i]) . "<br>";
}
echo "}";

function convertTableName($str)
{
    $tableName = str_replace(" {", "", str_replace("Table ", "", $str));
    return 'Schema::create("' . $tableName . '", function (Blueprint $table) {';
}

function convertColumn($str)
{
    $arr = explode(" ", $str);
    if ($arr[0] === 'id') {
        return '$table->id();';
    }

    $type = convertType($arr[0], $arr[1]);

    $attr = convertAttribute(array_slice($arr, 2));

    return '$table' . $type . $attr . ';';
}

function convertType($col, $type)
{
    $result = '';
    if (substr($type, 0, 4) === 'char') {
        return '->char("' . $col . '", ' . $type[5] . ')';
    }

    switch ($type) {
        case 'bigint':
            $result = 'bigInteger';
            break;
        case 'int':
            $result = 'integer';
            break;
        case 'tinyint':
            $result = 'tinyInteger';
            break;
        case 'string':
            $result = 'string';
            break;
        case 'date':
            $result = 'date';
            break;
        case 'text':
            $result = 'text';
            break;
        case 'boolean':
            $result = 'boolean';
            break;
        default:
            $result = 'unknown';
            break;
    }
    return '->' . $result . '("' . $col . '")';
}

function convertAttribute($arr) {
    $str = implode(' ', $arr);
    $result = '';
    if (!strpos($str, 'not null') && !strpos($str, 'ref: >')) {
        $result .= '->nullable()';
    }

    if (strpos($str, 'unique')) {
        $result .= '->unique()';
    }

    return $result;
}