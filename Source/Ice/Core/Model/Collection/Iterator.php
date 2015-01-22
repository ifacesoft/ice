<?php
/**
 * Ice core model collection iterator class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Iterator;

/**
 * Iterator for model collection
 *
 * @package Ice\Core\Model_Collection
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @version 0.4
 * @since 0.0
 */
class Model_Collection_Iterator implements Iterator
{
    /**
     * Model class
     *
     * @var Model
     */
    private $_modelClass = null;

    /**
     * Rows
     *
     * @var array
     */
    private $_rows = null;

    /**
     * Row index of iterator
     *
     * @var int
     */
    private $position = 0;

    /**
     * Constructor of model collection iterator
     *
     * @param Model $modelClass
     * @param array $rows
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    private function __construct($modelClass, $rows)
    {
        $this->_modelClass = $modelClass;
        $this->_rows = $rows;
    }

    /**
     * Create instance of iterator
     *
     * @param Model $modelClass
     * @param array $rows
     * @return Model_Collection_Iterator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public static function create($modelClass, $rows = [])
    {
        return new Model_Collection_Iterator($modelClass, $rows);
    }

    /**
     * Return the current row of iterator
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return Model Can return any type.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function current()
    {
        $modelClass = $this->_modelClass;
        return $modelClass::create(current($this->_rows))->clearAffected();
    }

    /**
     * Return next model of collection
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function next()
    {
        next($this->_rows);
        ++$this->position;
    }

    /**
     * Return index of iteration row
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return int scalar on success, or null on failure.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Validation current row position of iterator
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function valid()
    {
        $var = current($this->_rows);
        return !empty($var);
    }

    /**
     * Reset iterator
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function rewind()
    {
        if (!empty($this->_rows)) {
            reset($this->_rows);
        }

        $this->position = 0;
    }

    /**
     * Return iterated rows
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * Return collection iterator model class
     *
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getModelClass()
    {
        return $this->_modelClass;
    }

    /**
     * Add data in collection
     *
     * @param array $data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function add(array $data)
    {
        if (empty($data)) {
            return;
        }

        if (!is_numeric(each($data)[0])) {
            $data = [$data];
        }

        $modelClass = $this->getModelClass();

        foreach ($data as $row) {
            $newRow = [];
            foreach ($row as $fieldName => $fieldValue) {
                $newRow[$modelClass::getFieldName($fieldName)] = $fieldValue;
            }
            unset($row);
            $this->_rows[] = $newRow;
        }
    }

    /**
     * Define rows
     *
     * @param array $rows
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function setRows(array $rows)
    {
        $this->_rows = $rows;
    }
}