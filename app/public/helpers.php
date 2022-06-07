<?php

class Helpers
{
    public $enumTable = "Hello";

    function convertTable($array) {
        echo "<h2>" . $this->checkTypeTable($array[0]) . "</h2>";
        if ($this->checkTypeTable($array[0]) === 'enum') {
            return;
        }

        echo $this->convertTableName($array[0]) . "<br>";
        for ($i = 1; $i < count($array) - 1; $i++) {
            echo " &nbsp; &nbsp;" . $this->convertColumn($array[$i]) . "<br>";
        }
        echo "}<br>";
    }

    function checkTypeTable($firstLine) {
        if (substr($firstLine, 0, 4) === 'Enum') {
            return 'enum';
        }
        if (substr($firstLine, 0, 5) === 'Table') {
            return 'table';
        }
    }

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

        $type = $this->convertType($arr[0], $arr[1]);

        $attr = $this->convertAttribute(array_slice($arr, 2));

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
            case 'json':
                $result = 'json';
                break;
            default:
                $result = 'unknown';
                break;
        }
        return '->' . $result . '("' . $col . '")';
    }

    function convertAttribute($arr)
    {
        $arrAttr = $this->getArrayAttribute($arr);
        $result = '->nullable()';
        foreach ($arrAttr as $item) {
            $item = trim($item);
            if ($item === 'not null'
                || (strpos($item, 'ref: >') == 0 && strpos($item, 'ref: >') != false)
            ) {
                $result = str_replace('->nullable()', '', $result);
            }
            if (strpos($item, 'unique')) {
                $result .= '->unique()';
            }
            if (substr($item, 0, 4) === 'note') {
                $position = strpos($item, "'");
                $comment = '';
                for ($i = $position + 1; $i < strlen($item); $i++) {
                    if ($item[$i] === "'") {
                        break;
                    }
                    $comment .= $item[$i];
                }
                $result .= "->comment('" . $comment . "')";
            }
        }

        return $result;
    }

    function getArrayAttribute($arr)
    {
        $str = substr(implode(' ', $arr), 1, -1);
        return explode(',', $str);
    }
}
