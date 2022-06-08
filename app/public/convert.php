<?php

class Convert
{
    private $convertedEnum = [];

    public function __construct($tables, private $enumTable = [], private $tableTable = [])
    {
        foreach ($tables as $table) {
            $type = $this->getTypeTable($table[0]);
            if ($type === 'enum') {
                $this->enumTable[] = $table;
            }
            if ($type === 'table') {
                $this->tableTable[] = $table;
            }
        }
        $this->handleExport();
    }

    private function getTypeTable($firstLine)
    {
        if (substr($firstLine, 0, 4) === 'Enum') {
            return 'enum';
        }
        if (substr($firstLine, 0, 5) === 'Table') {
            return 'table';
        }
    }

    private function handleExport()
    {
        foreach($this->enumTable as $enum) {
            $this->convertEnum($enum);
        }
        foreach($this->tableTable as $table) {
            $this->convertTable($table);
        }
    }

    private function convertEnum()
    {
        
    }

    private function convertTable($array)
    {
        echo $this->convertTableName($array[0]) . "<br>";
        for ($i = 1; $i < count($array) - 1; $i++) {
            echo " &nbsp; &nbsp;" . $this->convertColumn($array[$i]) . "<br>";
        }
        echo "}<br>";
    }

    private function convertTableName($str)
    {
        $tableName = str_replace(" {", "", str_replace("Table ", "", $str));
        return 'Schema::create("' . $tableName . '", function (Blueprint $table) {';
    }

    private function convertColumn($str)
    {
        $arr = explode(" ", $str);
        if ($arr[0] === 'id') {
            return '$table->id();';
        }

        $type = $this->convertType($arr[0], $arr[1]);

        $attr = $this->convertAttribute(array_slice($arr, 2));

        return '$table' . $type . $attr . ';';
    }

    private function convertType($col, $type)
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

    private function convertAttribute($arr)
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

    private function getArrayAttribute($arr)
    {
        $str = substr(implode(' ', $arr), 1, -1);
        return explode(',', $str);
    }
}
