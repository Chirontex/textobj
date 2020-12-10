<?php
/**
 *    TextObj version 0.4.1
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

use Exception;

class Table implements TableInterface
{

    private $data;
    private $pathfile;

    public function __construct(string $pathfile = '')
    {
        
        $this->data = [];
        $this->pathfile = '';

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

            $header = explode(';', htmlspecialchars_decode(trim(array_shift($file))));

            foreach ($file as $row_number => $row_data) {
                
                $row_data = explode(';', htmlspecialchars_decode(trim($row_data)));

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
        else $row_number = $this->countRows() + $row_number + 1;

        foreach ($header as $column_name) {
            
            $result[$column_name] = $this->data[$column_name][$row_number];

        }

        return $result;

    }

    public function push(array $values) : void
    {

        foreach ($this->header() as $column) {
            
            if (empty($values[$column])) $this->data[$column][] = '';
            else $this->data[$column][] = (string)$values[$column];

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

    public function deleteColumn($column_marker) : void
    {

        if (!is_string($column_marker) &&
            !is_integer($column_marker)) throw new Exception(
                __CLASS__."::".__FUNCTION__.
                "() — invalid type of argument.", -3
            );

        unset($this->data[$column_marker]);

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
            $table_2 = array_slice($this->data, $column_number, null, true);

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

    public function where(array $conds) : array
    {

        $result = [];

        foreach ($conds as $suite) {

            $suite_keys = array_keys($suite);
            
            $row_key = array_search((string)$suite[$suite_keys[0]], $this->data[$suite_keys[0]], true);

            if ($row_key !== false) {

                $row_key += 1;

                $row = $this->row($row_key);

                $match = true;

                foreach ($suite as $key => $value) {
                    
                    if ((string)$value !== $row[$key]) {

                        $match = false;
                        break;

                    }

                }

                if ($match) $result[] = $row_key;

            }

        }

        return $result;

    }

    public function whereLike(array $conds) : array
    {

        $result = [];

        for ($i = 1; $i <= $this->countRows(); $i++) {

            $row = $this->row($i);

            $added = false;

            foreach ($conds as $suite) {

                if ($added) break;

                $match = true;
                
                foreach ($suite as $key => $value) {

                    if (empty($row[$key])) $match = false;
                    else {

                        if (strpos($value, '%') === false) $match = false;
                        else {

                            if (substr($value, 0, 1) === '%') {

                                if (strpos($row[$key], $value) === false) $match = false;
                                else {

                                    if ((
                                            strpos($row[$key], $value) +
                                            iconv_strlen($value)
                                        ) !== iconv_strlen($row[$key])) $match = false;

                                }

                            }

                            if ($match && (substr($value, -1, 1) === '%')) {

                                if (strpos($row[$key], $value) !== 0) $match = false;

                            }

                            if ($match &&
                                (strpos(trim($value, '%'), '%') !== false)) {

                                $value_exp = explode('%', trim($value, '%'));

                                for ($c = 0; $c < count($value_exp); $c++) {

                                    $pos = strpos($row[$key], $value_exp[$c]);

                                    if ($pos === false) $match = false;

                                    if ($match && ($c === 0)) {

                                        if (substr($value, 0, 1) !== '%' &&
                                            $pos !== 0) $match = false;

                                    } elseif ($match && (count($value_exp) - $c === 1)) {

                                        if (substr($value, -1) !== '%' &&
                                            (
                                                $pos +
                                                iconv_strlen($value_exp[$c]) !== iconv_strlen($row[$key])
                                            )) $match = false;

                                    } elseif ($match) {

                                        if (!($pos > strpos($row[$key], $value_exp[$c - 1]))) $match = false;

                                    }

                                    if (!$match) break;

                                }

                            }

                        }

                    }

                    if (!$match) break;

                }

                if ($match) {

                    $result[] = $i;

                    $added = true;

                }

            }

        }

        return $result;

    }

    public function update(int $row_number, array $values) : void
    {

        if ($row_number === 0) throw new Exception(
            __CLASS__."::".__FUNCTION__.
            "() — \$row_number argument points to the header.", -5
        );

        if ($row_number > 0) $row_number -= 1;
        elseif ($row_number < 0) $row_number = $this->countRows() + $row_number + 1;

        foreach ($values as $key => $value) {
            
            if (isset($this->data[$key])) $this->data[$key][$row_number] = $value;

        }

    }

    public function save(string $pathfile = '') : bool
    {

        if (empty($pathfile)) {

            if (empty($this->pathfile)) throw new Exception(
                __CLASS__."::".__FUNCTION__.
                "() — saving file was not specified.", -6
            );

            $pathfile = $this->pathfile;

        }

        if (substr($pathfile, -4) !== '.txt') throw new Exception(
            __CLASS__."::".__FUNCTION__.
            "() — invalid saving file extension.", -7
        );

        $header = $this->header();

        $data = implode(';', $header)."\n";

        for ($i = 0; $i < $this->countRows(); $i++) {

            $row = [];

            foreach ($header as $column_name) {
                
                $row[] = $this->data[$column_name][$i];

            }

            $data .= implode(';', $row)."\n";

        }

        if (file_put_contents($pathfile, $data) !== false) return true;
        else return false;

    }

    public function saveAs(string $pathfile) : bool
    {

        return $this->save($pathfile);

    }

    public function table(string $classes = '', string $id = '') : string
    {

        ob_start();

?>
<table class="<?= $classes ?>" id="<?= $id ?>">
    <thead>
        <tr>
<?php

        foreach ($this->header() as $column_name) {

?>
            <th><?= htmlspecialchars($column_name) ?></th>
<?php

        }

?>
        </tr>
    </thead>
    <tbody>
<?php

        for ($i = 0; $i < $this->countRows(); $i++) {

?>
        <tr>
<?php

            foreach ($this->header() as $column_name) {
                
?>
            <td><?= htmlspecialchars($this->data[$column_name][$i]) ?></td>
<?php

            }

?>
        </tr>
<?php

        }

?>
    </tbody>
</table>
<?php

        return ob_get_clean();
        
    }

}
