<?php
/**
 *    TextObj
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

interface TextObjInterface
{

    /**
     * Returns all the data.
     * 
     * @return array
     */
    public function data();

    /**
     * Returns header of the table.
     * 
     * @return array
     */
    public function header();

    /**
     * Returns the row.
     * 
     * @param int $row_number
     * If $row_number is negative, the method will return a row from
     * an end of the table.
     * 
     * @return array
     */
    public function row(int $row_number);

    /**
     * Add a new row in the end of the table.
     * 
     * @param array $values
     * 
     * @return void
     */
    public function push(array $values);

}
