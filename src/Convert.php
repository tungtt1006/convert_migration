<?php
namespace App;

class Convert
{
    private $enums = [];
    private $tables = [];
    private $convertedEnums = [];
    private $convertedTables = [];

    public function __construct($tables)
    {
        foreach ($tables as $table) {
            $type = $this->getTypeTable($table[0]);

            if ($type === 'enum') {
                $this->enums[] = $table;
            } elseif ($type === 'table') {
                $this->tables[] = $table;
            }
        }

        $this->handleConvert();
    }

    public function getTable()
    {
        return $this->convertedTables;
    }

    private function getTypeTable($firstLine)
    {
        if (strtolower(substr($firstLine, 0, 4)) === 'enum') {
            return 'enum';
        }

        if (strtolower(substr($firstLine, 0, 5)) === 'table') {
            return 'table';
        }
    }

    private function handleConvert()
    {
        foreach ($this->enums as $enum) {
            $this->convertEnum($enum);
        }

        foreach ($this->tables as $table) {
            $this->convertTable($table);
        }
    }

    private function convertEnum($enum)
    {
        $enumName = str_replace(" {", "", str_replace("Enum ", "", $enum[0]));
        $enumValue = '';
        for ($i = 1; $i < count($enum) - 1; $i++) {
            if ($i == 1) {
                $enumValue .= '"' . $enum[$i] . '"';
            } else {
                $enumValue .= ', "' . $enum[$i] . '"';
            }
        }
        $this->convertedEnums[$enumName] = '->enum("' . $enumName . '", [' . $enumValue . '])';
    }

    private function convertTable($array)
    {
        $tableStr = '';
        $convertedTableName = $this->convertTableName($array[0]);
        $tableStr .= $convertedTableName['content'] . "\r\n";

        for ($i = 1; $i < count($array) - 1; $i++) {
            $tableStr .= $this->convertColumn($array[$i]);
        }
        $tableStr .= "        });\r\n    }\r\n";
        $this->convertedTables[$convertedTableName['name']] = $tableStr;
    }

    private function convertTableName($str)
    {
        $tableName = str_replace(" {", "", str_replace("Table ", "", $str));
        return [
            'name' => $tableName,
            'content' => '        Schema::create("' . $tableName . '", function (Blueprint $table) {',
        ];
    }

    private function convertColumn($str)
    {
        $arr = explode(' ', $str);
        if ($arr[0] === 'id') {
            return '            $table->id();' . "\r\n";
        }

        $convertedType = $this->convertType($arr[0], $arr[1]);

        $convertedAttr = $this->convertAttribute(array_slice($arr, 2));

        return '            $table' . $convertedType . $convertedAttr . ';' . "\r\n";
    }

    private function convertType($colName, $type)
    {
        if (substr($type, 0, 4) === 'char') {
            return '->char("' . $colName . '", ' . $type[5] . ')';
        }

        if (isset($this->convertedEnums[$type])) {
            return $this->convertedEnums[$type];
        }

        $typeArr = [
            "bigint" => "bigInteger",
            "int" => "integer",
            "tinyint" => "tinyInteger",
            "string" => "string",
            "date" => "date",
            "text" => "text",
            "boolean" => "boolean",
            "json" => "json",
        ];

        return '->' . ($typeArr[$type] ??  'unknow') . '("' . $colName . '")';
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
