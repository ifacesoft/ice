<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 30.12.13
 * Time: 23:41
 */

namespace ice\core;

use ice\core\model\Collection;
use ice\Exception;
use Iterator;

class Data implements Iterator
{
    const RESULT_MODEL_CLASS = 'modelName';
    const RESULT_ROWS = 'rows';
    const RESULT_SQL = 'sql';
    const NUM_ROWS = 'foundRows';
    const AFFECTED_ROWS = 'affectedRows';
    const INSERT_ID = 'insertId';
    private $key = 0;

    private $_result = array(
        self::RESULT_MODEL_CLASS => null,
        self::RESULT_ROWS => array(),
        self::RESULT_SQL => '',
        self::NUM_ROWS => 0,
        self::AFFECTED_ROWS => 0,
    );

    public function __construct(array $result)
    {
        $this->_result = array_merge($this->_result, $result);
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        /** @var Model $modelName */
        $modelName = $this->_result[self::RESULT_MODEL_CLASS];

        $collection = $modelName::getCollection();
        $collection->setData($this);

        return $collection;
    }

    /**
     * @desc Результат запроса - единственное значение.
     * @param null $columnName
     * @return mixed
     */
    public function getValue($columnName = null)
    {
        $row = $this->getRow();
        return $row ? ($columnName ? $row[$columnName] : reset($row)) : null;
    }

    /**
     * @desc Результат запроса - единственная запись таблицы.
     * @param null $pk
     * @return array|null
     */
    public function getRow($pk = null)
    {
        $rows = $this->getRows();

        if (empty($rows)) {
            return null;
        }

        if (isset($pk)) {
            return isset($rows[$pk]) ? $rows[$pk] : null;
        }

        return reset($rows);

    }

    /**
     * @return array
     */
    public function getRows()
    {
        $rows = $this->_result[self::RESULT_ROWS];
        return empty($rows) ? array() : $rows;
    }

    public function getModel()
    {
        $row = $this->getRow();

        if (empty($row)) {
            return null;
        }

        /** @var Model $modelName */
        $modelName = $this->_result[self::RESULT_MODEL_CLASS];

        return $modelName::create($row);
    }

    public function addRow(array $row)
    {
        /** @var Model $modelName */
        $modelName = $this->_result[self::RESULT_MODEL_CLASS];

        $this->_result[DATA::RESULT_ROWS][$row[$modelName::getPkName()]] = $row;
        $this->_result[DATA::NUM_ROWS]++;
    }

    public function getFoundRows()
    {
        return $this->_result[DATA::NUM_ROWS];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        /** @var Model $modelClass */
        $modelClass = $this->_result[DATA::RESULT_MODEL_CLASS];
        return $modelClass::create(current($this->_result[DATA::RESULT_ROWS]));
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->_result[DATA::RESULT_ROWS]);
        $this->key++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @throws Exception
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return !empty(current($this->_result[DATA::RESULT_ROWS]));
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        if (!empty($this->_result[DATA::RESULT_ROWS])) {
            reset($this->_result[DATA::RESULT_ROWS]);
            $this->key = 0;
        }
    }

    public function getKeys()
    {
        return array_keys($this->_result[DATA::RESULT_ROWS]);
    }

    public function delete($pk)
    {
        $row = $this->_result[DATA::RESULT_ROWS][$pk];
        unset($this->_result[DATA::RESULT_ROWS][$pk]);
        return $row;
    }

    public function getSql()
    {
        return $this->_result[DATA::RESULT_SQL];
    }
}