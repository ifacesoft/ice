<?php
/**
 * Ice core model collection iterator class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
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
 * @since   0.0
 */
class Model_Collection_Iterator implements Iterator
{
    /**
     * Model class
     *
     * @var Model
     */
    private $modelClass = null;

    /**
     * Rows
     *
     * @var array
     */
    private $rows = null;

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
     * @since   0.0
     */
    private function __construct($modelClass, $rows)
    {
        $this->modelClass = $modelClass;
        $this->rows = $rows;
    }

    /**
     * Create instance of iterator
     *
     * @param  Model $modelClass
     * @param  array $rows
     * @return Model_Collection_Iterator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
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
     *
     * @link   http://php.net/manual/en/iterator.current.php
     * @return Model Can return any type.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function current()
    {
        $modelClass = $this->modelClass;
        return $modelClass::create(current($this->rows))->clearAffected();
    }

    /**
     * Return next model of collection
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     *
     * @link   http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function next()
    {
        next($this->rows);
        ++$this->position;
    }

    /**
     * Return index of iteration row
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     *
     * @link   http://php.net/manual/en/iterator.key.php
     * @return int scalar on success, or null on failure.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
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
     *
     * @link   http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function valid()
    {
        $var = current($this->rows);
        return !empty($var);
    }

    /**
     * Reset iterator
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     *
     * @link   http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function rewind()
    {
        if (!empty($this->rows)) {
            reset($this->rows);
        }

        $this->position = 0;
    }

    public static function getConfig()
    {
        return Config::getInstance(get_called_class());
    }
}
