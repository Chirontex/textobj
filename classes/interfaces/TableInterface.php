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
namespace DRNoisier\TextObj;

interface TableInterface
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

    /**
     * Returns the column.
     * 
     * @param string|int $column_marker
     * 
     * @return array
     */
    public function column($column_marker);

    /**
     * Insert new column.
     * 
     * @param string $column_name
     * If column with this name already exist in the table,
     * the method will throw an Exception.
     * @param int $column_number
     * Periodic number of the new column. If $column_number = 0,
     * new column will be added at the end of the table.
     * 
     * @return void
     */
    public function insert(string $column_name, int $column_number = 0);

    /**
     * Returns rows amount.
     * 
     * @return int
     */
    public function countRows();

    /**
     * Returns columns amount.
     * 
     * @return int
     */
    public function countColumns();

    /**
     * Returns rows numbers based on conditions.
     * 
     * @param array $conds
     * $conds structure must be like:
     * [
     * 
     *  [column_name => value, ...],
     * 
     *  [...]
     * 
     * ]
     * 
     * Conditions united with logical AND must be in the one array.
     * 
     * Conditions united with logical OR must be in separate arrays.
     * 
     * @return array
     */
    public function where(array $conds);

    /**
     * Updates a specified row.
     * 
     * @param int $row_number
     * @param array $values
     * 
     * @return void
     */
    public function update(int $row_number, array $values);

    /**
     * Save data in a file.
     * 
     * @param string $pathfile
     * 
     * @return bool
     */
    public function save(string $pathfile = '');

    /**
     * Save method with mandatory $pathfile.
     * 
     * @param string $pathfile
     * 
     * @return bool
     */
    public function saveAs(string $pathfile);

    /**
     * Output the data in HTML-table.
     * 
     * @return string
     */
    public function table();

}
