<?php
/**
 * Ice core data class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Data\Provider\Cacher;
use Ice\Helper\Arrays;
use Ice\Helper\Serializer;

/**
 * Class Data
 *
 * Core Data class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Query_Result implements Cacheable
{
    use Stored;

    const ROWS = 'rows';
    const NUM_ROWS = 'numRows';
    const AFFECTED_ROWS = 'affectedRows';
    const FOUND_ROWS = 'foundRows';
    const INSERT_ID = 'insertId';
    const QUERY_BODY = 'queryBody';
    const QUERY_PARAMS = 'queryParams';

    /**
     * Default result
     *
     * @var array
     */
    protected $_default = [
        Query_Result::ROWS => [],
        Query_Result::NUM_ROWS => 0,
        Query_Result::AFFECTED_ROWS => 0,
        Query_Result::FOUND_ROWS => 0,
        Query_Result::INSERT_ID => null,
        Query_Result::QUERY_BODY => null,
        Query_Result::QUERY_PARAMS => []
    ];

    /**
     * Result
     *
     * @var array
     */
    private $result = [];
    /**
     * @var Query
     */
    private $query = null;

    /**
     * Attached transformations
     *
     * @var array
     */
    private $transformations = [];

    /**
     * Constructor of data object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    private function __construct()
    {
    }

    /**
     * Return data from cache
     *
     * @param  Query $query
     * @param  array $result
     * @return Query_Result
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public static function create(Query $query, array $result = [])
    {
        $queryResult = new Query_Result();

        $queryResult->query = $query;
        $queryResult->result = Arrays::defaults($queryResult->_default, $result);

        return $queryResult;
    }

    public static function invalidateCache($invalidateTags)
    {
        Cache::invalidate(__CLASS__, $invalidateTags);
    }

    /**
     * Return query result cacher
     *
     * @param string $index
     * @return Cacher
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   0.5
     */
    public static function getCacher($index = 'default')
    {
        return Cacher::getInstance(__CLASS__, $index);
    }

    /**
     * Return all rows from data as array
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getRows()
    {
        return $this->getResult()[self::ROWS];
    }

    /**
     * Result data
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    private function getResult()
    {
        if ($this->transformations === null) {
            return $this->result;
        }

        $this->result[self::ROWS] = $this->applyTransformations($this->result[self::ROWS]);

        return $this->result;
    }

    /**
     * Apply all attached transformations
     *
     * @param  $rows
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    private function applyTransformations($rows)
    {
        if (empty($this->transformations)) {
            $this->transformations = null;
            return $rows;
        }

        $transformData = [];
        foreach ($this->transformations as $transformation) {
            list($transformationName, $params) = $transformation;
            $transformData[] = Data_Transformation::getInstance($transformationName)
                ->transform($this->getModelClass(), $rows, $params);
        }

        foreach ($rows as $key => &$row) {
            foreach ($transformData as $transform) {
                $row = array_merge($row, $transform[$key]);
            }
        }

        $this->transformations = null;
        return $rows;
    }

    /**
     * Return count of rows returned by query
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getNumRows()
    {
        return $this->result[Query_Result::NUM_ROWS];
    }

    /**
     * Remove row from data by pk
     *
     * @param  $pk
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function delete($pk = null)
    {
        if (empty($pk)) {
            $this->result[Query_Result::ROWS] = [];
            return [];
        }

        $row = $this->result[Query_Result::ROWS][$pk];
        unset($this->result[Query_Result::ROWS][$pk]);

        return $row;
    }

    /**
     * Attach data transformation
     *
     * @param  $transformation
     * @param  $params
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function addTransformation($transformation, $params)
    {
        if ($this->transformations === null) {
            $this->transformations = [];
        }

        $this->transformations[] = [$transformation, $params];
        return $this;
    }

    /**
     * Filter data
     *
     * @param  $filterScheme
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function filter($filterScheme)
    {
        $data = clone $this;
        $data->result[Query_Result::ROWS] = Arrays::filter($data->result[Query_Result::ROWS], $filterScheme);
        return $data;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link   http://php.net/manual/en/arrayaccess.offsetget.php
     * @param  mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->result[Query_Result::ROWS][$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link   http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param  mixed $offset <p>
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
     * @since   0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->result[Query_Result::ROWS][$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link   http://php.net/manual/en/arrayaccess.offsetset.php
     * @param  mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param  mixed $value <p> The value to set. </p> The value to set. </p>
     * The value to set.
     * </p>
     * @return void
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->result[Query_Result::ROWS][] = $value;
        } else {
            $this->result[Query_Result::ROWS][$offset] = $value;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link   http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param  mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->result[Query_Result::ROWS][$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link   http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function count()
    {
        return count($this->result[Query_Result::ROWS]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link   http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function serialize()
    {
        return Serializer::serialize($this->result);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     *
     * @link   http://php.net/manual/en/serializable.unserialize.php
     * @param  string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function unserialize($serialized)
    {
        $this->result = Serializer::unserialize($serialized);
    }

    /**
     * Return inserted id
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getInsertId()
    {
        return $this->result[Query_Result::INSERT_ID];
    }

    /**
     * Return random key
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getRandKey()
    {
        return array_rand($this->getResult()[Query_Result::ROWS]);
    }

    /**
     * Return count of affectd rows
     *
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     */
    public function getAffectedRows()
    {
        return $this->result[Query_Result::AFFECTED_ROWS];
    }

    /**
     * Return query body
     *
     * @return string|array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getQueryBody()
    {
        return $this->result[Query_Result::QUERY_BODY];
    }

    /**
     * Return query params
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getQueryParams()
    {
        return $this->result[Query_Result::QUERY_PARAMS];
    }

    /**
     * @param $time
     * @return Cacheable
     */
    public function validate($time)
    {
        return Cache::validateTimeTags($this, $this->getQuery()->getValidateTags(), $time);
    }

    /**
     * Return query of query result
     *
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return Cacheable
     */
    public function invalidate()
    {
        return Cache::invalidateTimeTags($this, $this->getQuery()->getInvalidateTags());
    }

    public function __toString()
    {
        $string = '';

        try {
            $string = print_r($this->getQuery()->getBody(), true) .
                ' (' . implode(', ', $this->getQuery()->getBinds()) . ') result: \'' .
                Query_Result::NUM_ROWS . '\' => ' . $this->getNumRows() . ', \'' .
                Query_Result::AFFECTED_ROWS . '\' => ' . $this->getAffectedRows() . ', \'' .
                Query_Result::FOUND_ROWS . '\' => ' . $this->getFoundRows() . ', \'' .
                Query_Result::INSERT_ID . '\' => ' . print_r($this->getInsertId(), true);
        } catch (\Exception $e) {
            Query_Result::getLogger()->error('fail', __FILE__, __LINE__, $e);
        }

        return $string;
    }

    public function getFoundRows()
    {
        return $this->result[Query_Result::FOUND_ROWS];
    }
    //
    //    public function getPagination()
    //    {
    //        $pagination = ['foundRows' => $this->getFoundRows()];
    //
    //        $limit = $this->getQuery()->getBodyParts()[Query_Builder::PART_LIMIT];
    //
    //        if (empty($limit)) {
    //            $pagination['page'] = 1;
    //            $pagination['limit'] = 0;
    //        } else {
    //            list($limit, $offset) = $limit;
    //            $pagination['page'] = $offset ? $offset / $limit + 1 : 1;
    //            $pagination['limit'] = $limit;
    //        }
    //
    //        return $pagination;
    //    }
}
