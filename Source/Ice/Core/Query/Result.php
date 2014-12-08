<?php
/**
 * Ice core data class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use ArrayAccess;
use Countable;
use Ice\Core;
use Ice\Core\Model\Collection;
use Ice\Helper\Arrays;
use Ice\Helper\Console;
use Ice\Helper\Memory;
use Ice\Helper\Serializer;
use Iterator;
use Serializable;

/**
 * Class Data
 *
 * Core Data class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.2
 * @since 0.2
 */
class Query_Result extends Container implements Iterator, ArrayAccess, Countable, Serializable, Cacheable
{
    use Core;

    const RESULT_MODEL_CLASS = 'modelName';
    const RESULT_ROWS = 'rows';
    const QUERY_FULL_HASH = 'query_hash';
    const NUM_ROWS = 'numRows';
    const AFFECTED_ROWS = 'affectedRows';
    const INSERT_ID = 'insertId';
    const FOUND_ROWS = 'foundRows';
    const PAGE = 'page';
    const LIMIT = 'limit';

    /**
     * Default data
     *
     * @var array
     */
    protected $_result = [
        self::RESULT_MODEL_CLASS => null,
        self::RESULT_ROWS => [],
        self::QUERY_FULL_HASH => '',
        self::NUM_ROWS => 0,
        self::FOUND_ROWS => 0,
        self::AFFECTED_ROWS => 0,
        self::PAGE => 1,
        self::LIMIT => 1000,
        self::INSERT_ID => null
    ];

    /**
     * Valid data flag
     *
     * @var bool
     */
    private $isValid = false;

    /**
     * Row index of iterator
     *
     * @var int
     */
    private $position = 0;

    /**
     * Attached transformations
     *
     * @var array
     */
    private $_transformations = [];

    /**
     * Constructor of data object
     *
     * @param array $result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function __construct(array $result)
    {
        $this->_result = array_merge($this->_result, $result);
        $this->isValid = true;
    }

    /**
     * Return data from cache
     *
     * @param $data
     * @param $hash
     * @return Query_Result
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getCache($data, $hash)
    {
        /** @var Query $query */
        list($query, $ttl) = $data;

        $queryType = $query->getQueryType();
        if ($queryType == Query_Builder::TYPE_SELECT && !$ttl) {
            return Query_Result::create($data);
        }

        if (Environment::isDevelopment()) {
            $message = 'sql: ' . str_replace("\t", '', str_replace("\n", ' ', $query->getSql())) . ' [' . implode(', ', $query->getBinds()) . ']';

            if (Request::isCli()) {
                Query::getLogger()->info($message . ' ' . Memory::memoryGetUsagePeak(), Logger::GREY, false);
            } else {
                fb($message);
            }
        }

        switch ($queryType) {
            case Query_Builder::TYPE_SELECT:
                $cacheDataProvider = Query::getDataProvider('query');
                $cache = $cacheDataProvider->get($hash);

                if (!$cache) {
                    $cache = ['tags' => $query->getValidateTags(), 'time' => 0, 'data' => []];
                }

                if (Cache::validate(__CLASS__, $cache['tags'], $cache['time'])) {
                    if (Environment::isDevelopment()) {
                        Query::getLogger()->info('Data from cache!', Logger::MESSAGE, false);
                    }

                    return $cache;
                }

                $cache['data'] = $query->getDataSource()->$queryType($query);
                $cache['time'] = time();

                $cacheDataProvider->set($hash, $cache);
                break;

            case Query_Builder::TYPE_INSERT:
            case Query_Builder::TYPE_UPDATE:
            case Query_Builder::TYPE_DELETE:
                $cache['data'] = $query->getDataSource()->$queryType($query);
                Cache::invalidate(__CLASS__, $query->getInvalidateTags());
                break;

            default:
                throw new Exception('Unknown data source query statment type "' . $queryType . '"');
        }

        $data = new Query_Result($cache['data']);

        return $data;
    }

    /**
     * Create new instance of data
     *
     * @param $data
     * @param null $hash
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($data, $hash = null)
    {
        list($query, $ttl) = $data;
        $queryType = $query->getQueryType();

        if (Environment::isDevelopment()) {
            $message = 'sql: ' . str_replace("\t", '', str_replace("\n", ' ', $query->getSql())) . ' [' . implode(', ', $query->getBinds()) . ']';

            if (Request::isCli()) {
                Query::getLogger()->info($message . ' ' . Memory::memoryGetUsagePeak(), Logger::GREY, false);
            } else {
                fb($message);
            }
        }

        $data = new Query_Result($query->getDataSource()->$queryType($query));
        return $data;
    }

    /**
     * Return all rows from data as array
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getRows()
    {
        $rows = $this->getResult()[self::RESULT_ROWS];
        return empty($rows) ? [] : $rows;
    }

    /**
     * Result data
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function getResult()
    {
        if ($this->_transformations === null) {
            return $this->_result;
        }

        $this->_result[self::RESULT_ROWS] = $this->applyTransformations($this->_result[self::RESULT_ROWS]);

        return $this->_result;
    }

    /**
     * Apply all attached transformations
     *
     * @param $rows
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function applyTransformations($rows)
    {
        if (empty($this->_transformations)) {
            $this->_transformations = null;
            return $rows;
        }

        $transformData = [];
        foreach ($this->_transformations as $transformation) {
            list($transformationName, $params) = $transformation;
            $transformData[] = Data_Transformation::getInstance($transformationName)
                ->transform($this->getModelClass(), $rows, $params);
        }

        foreach ($rows as $key => &$row) {
            foreach ($transformData as $transform) {
                $row = array_merge($row, $transform[$key]);
            }
        }

        $this->_transformations = null;
        return $rows;
    }

    /**
     * Return target model class of data
     *
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getModelClass()
    {
        return $this->getResult()[self::RESULT_MODEL_CLASS];
    }

    /**
     * Get collection from data
     *
     * @return Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getCollection()
    {
        $modelClass = $this->getModelClass();
        $collection = $modelClass::getCollection();
        $collection->setData($this);
        return $collection;
    }

    /**
     * Get value from data
     *
     * @desc Результат запроса - единственное значение.
     *
     * @param null $columnName
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getValue($columnName = null)
    {
        $row = $this->getRow();
        return $row ? ($columnName ? $row[$columnName] : reset($row)) : null;
    }

    /**
     * Get first row from data
     *
     * @desc Результат запроса - единственная запись таблицы.
     *
     * @param null $pk
     * @return array|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getRow($pk = null)
    {
        $rows = $this->getResult()[self::RESULT_ROWS];

        if (empty($rows)) {
            return null;
        }

        if (isset($pk)) {
            return isset($rows[$pk]) ? $rows[$pk] : null;
        }

        return reset($rows);
    }

    /**
     * Return model from data
     *
     * @param null $pk
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getModel($pk = null)
    {
        $row = $this->getRow($pk);

        if (empty($row)) {
            return null;
        }

        $modelClass = $this->getModelClass();

        return $modelClass::create($row);
    }

    /**
     * Add row to data
     *
     * @param $pk
     * @param $fieldName
     * @param null $value
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function setRow($pk, $fieldName, $value = null)
    {
        $row = isset($this->_result[Query_Result::RESULT_ROWS][$pk])
            ? $this->_result[Query_Result::RESULT_ROWS][$pk] : [];

        if (is_array($fieldName)) {
            $row = array_merge($row, $fieldName);
        } else {
            $row[$fieldName] = $value;
        }

        $this->_result[Query_Result::RESULT_ROWS][$pk] = $row;
        $this->isValid = false;
    }

    /**
     * Return count of rows returned by query
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getNumRows()
    {
        return $this->_result[Query_Result::NUM_ROWS];
    }

    /**
     * Return the current row of iterator
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function current()
    {
        return current($this->_result[Query_Result::RESULT_ROWS]);
    }

    /**
     * Move forward to next row of iterator
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function next()
    {
        next($this->_result[Query_Result::RESULT_ROWS]);
        ++$this->position;
    }

    /**
     * Return index of iterator row
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @throws Exception
     * @return mixed scalar on success, or null on failure.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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
     * @version 0.0
     * @since 0.0
     */
    public function valid()
    {
        $var = current($this->_result[Query_Result::RESULT_ROWS]); // todo: may be (bool) current($this->_result[DATA::RESULT_ROWS])
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
     * @version 0.0
     * @since 0.0
     */
    public function rewind()
    {
        if (!empty($this->getResult()[Query_Result::RESULT_ROWS])) {
            reset($this->_result[Query_Result::RESULT_ROWS]);
        }

        $this->position = 0;
    }

    /**
     * Remove row from data by pk
     *
     * @param $pk
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function delete($pk = null)
    {
        if (empty($pk)) {
            $this->_result[Query_Result::RESULT_ROWS] = [];
            return [];
        }

        $row = $this->_result[Query_Result::RESULT_ROWS][$pk];
        unset($this->_result[Query_Result::RESULT_ROWS][$pk]);

        return $row;
    }

    /**
     * Attach data transformation
     *
     * @param $transformation
     * @param $params
     * @return $this
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function addTransformation($transformation, $params)
    {
        if ($this->_transformations === null) {
            $this->_transformations = [];
        }

        $this->_transformations[] = [$transformation, $params];
        return $this;
    }

    /**
     * Filter data
     *
     * @param $filterScheme
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function filter($filterScheme)
    {
        $data = clone $this;
        $data->_result[Query_Result::RESULT_ROWS] = Arrays::filter($data->_result[Query_Result::RESULT_ROWS], $filterScheme);
        return $data;
    }

    /**
     * Return count all found rows
     *
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getFoundRows()
    {
        return $this->_result[Query_Result::FOUND_ROWS];
    }

    /**
     * Return column in data
     *
     * @param null $fieldName
     * @param null $indexKey
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getColumn($fieldName = null, $indexKey = null)
    {
        return empty($fieldName)
            ? $this->getKeys()
            : Arrays::column($this->_result[Query_Result::RESULT_ROWS], $fieldName, $indexKey);
    }

    /**
     * Return keys of data
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getKeys()
    {
        return array_keys($this->_result[Query_Result::RESULT_ROWS]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->_result[Query_Result::RESULT_ROWS][$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->_result[Query_Result::RESULT_ROWS][$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_result[Query_Result::RESULT_ROWS][] = $value;
        } else {
            $this->_result[Query_Result::RESULT_ROWS][$offset] = $value;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->_result[Query_Result::RESULT_ROWS][$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function count()
    {
        return count($this->_result[Query_Result::RESULT_ROWS]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function serialize()
    {
        return Serializer::serialize($this->_result);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function unserialize($serialized)
    {
        $this->_result = Serializer::unserialize($serialized);
    }

    /**
     * Return inserted id
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getInsertId()
    {
        return $this->_result[Query_Result::INSERT_ID];
    }

    /**
     * Return random key
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getRandKey()
    {
        return array_rand($this->getResult()[Query_Result::RESULT_ROWS]);
    }

    /**
     * Return data limit
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getLimit()
    {
        return $this->_result[Query_Result::LIMIT];
    }

    /**
     * Return data page
     *
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getPage()
    {
        return $this->_result[Query_Result::PAGE];
    }
}