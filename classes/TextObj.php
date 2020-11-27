<?php
/**
 *    TextObj version 0.2
 *    
 *    Copyright (C) 2020  Dmitry Shumilin (dr.noisier@yandex.ru)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace DRNoisier;

use Exception;

class TextObj implements TextObjInterface
{

    private $data;
    private $pathfile;

    public function __construct(string $pathfile = '')
    {
        
        $this->data = [];

        if (!empty($pathfile)) {

            if (!file_exists($pathfile) ||
                substr($pathfile, -4) !== '.txt') throw new Exception(
                    __CLASS__." — incorrect file or file not exist.", -1
                );

            $file = file_get_contents($pathfile);

            if ($file === false) throw new Exception(
                __CLASS__." — unknown error occured while opening file.", -2
            );

            $this->pathfile = $pathfile;

            $file = explode("\n", $file);

            $header = explode(';', trim(array_shift($file)));

            foreach ($file as $row_number => $row_data) {
                
                $row_data = explode(';', trim($row_data));

                foreach ($header as $column_number => $column_name) {
                    
                    $this->data[empty($column_name) ?
                    $column_number :
                    $column_name][(int)$row_number] = $row_data[(int)$column_number];

                }

            }

        }

    }

    public function data() : array
    {

        return $this->data;

    }

    public function header() : array
    {

        return array_keys($this->data);

    }

    public function row(int $row_number) : array
    {

        $result = [];

        $header = $this->header();

        if ($row_number > 0) $row_number -= 1;
        elseif ($row_number === 0) return $header;
        else $row_number = $this->countRows() + ($row_number + 1);

        foreach ($header as $column_name) {
            
            $result[$column_name] = $this->data[$column_name][$row_number];

        }

        return $result;

    }

    public function push(array $values) : void
    {

        foreach ($values as $key => $value) {
            
            if (isset($this->data[$key])) $this->data[$key][] = $value;

        }

    }

    public function column($column_marker) : array
    {

        if (!is_string($column_marker) &&
            !is_integer($column_marker)) throw new Exception(
                __CLASS__."::".__FUNCTION__.
                "() — invalid type of argument.", -3
            );

        $result = [];

        if (isset($this->data[$column_marker])) $result = $this->data[$column_marker];

        return $result;

    }

    public function insert(string $column_name, int $column_number = 0) : void
    {

        if (isset($this->data[$column_name])) throw new Exception(
            __CLASS__."::".__FUNCTION__.
            "() — this column already exists.", -4
        );

        if ($column_number === 0) $this->data[$column_name] = [];
        else {

            if ($column_number > 0) $column_number -= 1;
            else $column_number = $this->countColumns() + $column_number;

            $table_1 = array_slice($this->data, 0, $column_number, true);
            $table_2 = array_slice($this->data, $column_number + 1, null, true);

            $insertion = [$column_name => []];

            $this->data = array_merge($table_1, $insertion, $table_2);

        }

    }

    public function countRows() : int
    {

        $result = 0;

        foreach ($this->data as $key => $value) {
            
            if (count($value) > $result) $result = count($value);

        }

        return $result;

    }

    public function countColumns() : int
    {

        return count($this->data);

    }

}
