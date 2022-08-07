<?php

namespace App;

use Exception;

class InputFile
{
    private $inputFile;

    public function __construct($path)
    {
        if (!file_exists($path)) {
            throw new Exception('File not found');
        }

        $this->inputFile = fopen($path, 'r');
    }

    public function __destruct()
    {
        fclose($this->inputFile);
    }

    public function convertFileToTables()
    {
        while (!feof($this->inputFile)) {
            $buffer = trim(fgets($this->inputFile));
            if ($buffer != '') {
                $arr[] = $buffer;
            }

            if ($buffer === '}') {
                $matches[] = $arr;
                $arr = [];
            }
        }

        if (!$this->validateStructureTables($matches)) {
            throw new Exception('Syntax error: Check your input file back');
        }

        return $matches;
    }

    function validateStructureTables($tables)
    {
        foreach ($tables as $table) {
            $lastIndex = count($table) - 1;

            if (!$this->isValidatedFirstRow($table[0])
                || !$this->isValidatedLastRow($table[$lastIndex])
            ) {
                return false;
            }

            for ($i = 1; $i < $lastIndex; $i++) {
                if (!$this->isValidatedCenterRow($table[$i])) {
                    return false;
                }
            }
        }

        return true;
    }

    private function isValidatedFirstRow($firstRow)
    {
        $arr = explode(' ', $firstRow);

        return count($arr) === 3
            && strtolower($arr[0]) === 'table'
            && $arr[2] === '{';
    }

    private function isValidatedLastRow($lastRow)
    {
        return $lastRow === '}';
    }

    private function isValidatedCenterRow($centerRow)
    {
        return !str_contains($centerRow, '{') && !str_contains($centerRow, '}');
    }
}
