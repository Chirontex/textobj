<?php
/**
 *    TextObj version 0.1
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

}
