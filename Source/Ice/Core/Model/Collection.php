<?php
/**
 * Ice core model collection class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use IteratorAggregate;
use Traversable;

/**
 * Class Model_Collection
 *
 * Core model collection class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.2
 * @since 0.0
 */
class Model_Collection implements IteratorAggregate
{
    use Core;

    /**
     * Data of model collection
     *
     * @var Query_Result
     */
    private $_queryResult = null;

    /**
     * Private constructor for model collection
     *
     * @param Query_Result $queryResult
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    private function __construct(Query_Result $queryResult)
    {
        $this->_queryResult = $queryResult;
    }

    /**
     * Create new instance of model collection
     *
     * @param Query_Result $queryResult
     * @return Model_Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public static function create(Query_Result $queryResult = null)
    {
        return new Model_Collection($queryResult);
    }

    /**
     * Return size (count potential models) of model collection
     *
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function getCount()
    {
        if ($this->_queryResult === null) {
            return 0;
        }

        return $this->_queryResult->count();
    }

    /**
     * Return raw data
     *
     * @return Query_Result
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function getQueryResult()
    {
        return $this->_queryResult;
    }

//    /**
//     * Return query builder of model collection
//     *
//     * @return Query_Builder
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @version 0.0
//     * @since 0.0
//     */
//    public function getQueryBuilder()
//    {
//        if ($this->_queryBuilder !== null) {
//            return $this->_queryBuilder;
//        }
//
//        if ($this->_queryResult !== null) {
//            return null;
//        }
//
//        /** @var Model $modelClass */
//        $modelClass = $this->_modelClass;
//
//        $this->_queryBuilder = $modelClass::getQueryBuilder();
//        return $this->_queryBuilder;
//    }

    /**
     * Return first model of model collection if not empty
     *
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function first()
    {
        if ($this->_queryResult === null) {
            return null;
        }

        $row = $this->_queryResult->getRow();

        if (!$row) {
            return null;
        }

        $modelClass = $this->_queryResult->getModelClass();

        return $modelClass::create($row);
    }

    /**
     * Return row from data in model collection
     *
     * @param mixed $pk
     * @return array|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @deprecated 0.2
     * @version 0.2
     * @since 0.0
     */
    public function getRow($pk = null)
    {
        if ($this->_queryResult === null) {
            return null;
        }

        return $this->_queryResult->getRow($pk);
    }

    /**
     * Add model to model collection
     *
     * @param Model $model
     * @return Model_Collection
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function add(Model $model)
    {
        if ($this->_queryResult !== null) {
            Model_Collection::getLogger()->fatal('Could not add item in collection created by query', __FILE__, __LINE__);
        }

        $this->_queryResult = new Query_Result([Query_Result::RESULT_MODEL_CLASS => get_class($model)]);

        $this->_queryResult->setRow($model->getPk(), $model->get());

        return $this;
    }

    /**
     * Insert rows-models to data source
     *
     * @param string|null $sourceName
     * @return Model_Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo need refactoring. Not work. ERROR: Sql query is empty ;)
     * @version 0.2
     * @since 0.0
     */
    public function insert($sourceName = null)
    {
        /** @var Model $modelClass */
        $modelClass = $this->_modelClass;

        $this->setQueryResult(
            $modelClass::getQueryBuilder()
                ->insert($this->getQueryResult()->getRows(), false, $sourceName)
                ->getQuery($sourceName)
        );

        return $this;
    }

    /**
     * Update rows-models in data source
     *
     * @param $updates
     * @param null $sourceName
     * @param int $ttl
     * @return $this
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo need refactoring. Not work. ERROR: Sql query is empty ;)
     * @version 0.0
     * @since 0.0
     */
    public function update($updates, $sourceName = null, $ttl = 3600)
    {
        if (empty($updates)) {
            return $this;
        }

        /** @var Model $modelClass */
        $modelClass = $this->_modelClass;
        $keys = $this->getKeys();

        $queryBuilder = $modelClass::getQueryBuilder();

        if (count($keys) == 1) {
            $queryBuilder->eq('/pk', reset($keys));
        } else {
            $queryBuilder->in('/pk', $keys);
        }

        $this->setQueryResult($queryBuilder->update($updates, $sourceName, $ttl));

        return $this;
    }

    /**
     * Return primary keys of all models in model collection
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function getKeys()
    {
        if ($this->_queryResult === null) {
            return [];
        }

        return $this->_queryResult->getKeys();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getIterator()
    {
        return new Model_Collection_Iterator($this->getQueryResult());
    }

    /**
     * Receive model from model collection by primary key
     *
     * @param $pk
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function get($pk)
    {
        $row = $this->_queryResult->getRow($pk);

        if (!$row) {
            return null;
        }

        $modelClass = $this->_queryResult->getModelClass();

        return $modelClass::create($row);
    }

    /**
     * Remove model from collection
     *
     * @param null $pk
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo need refactoring. Not work. ERROR: Sql query is empty ;)
     * @version 0.0
     * @since 0.0
     */
    public function remove($pk = null)
    {
        /** @var Model $modelClass */
        $modelClass = $this->_modelClass;

        return $modelClass::create($this->getQueryResult()->delete($pk));
    }

    /**
     * Filter model collection by filterScheme
     *
     * example filter scheme:
     * ```php
     *      $filterScheme = [
     *          ['name', 'Petya', '='],
     *          ['age', 18, '>'],
     *          ['surname', 'Iv%', 'like']
     *      ];
     * ```
     *
     * example usage:
     * ```php
     *  ->filter('name', 'Petya')
     *  ->filter(['age', 18, '>'])
     *  ->filter([['surname', 'Iv%', 'like']])
     * ```
     *
     * @see Arrays::filter()
     *
     * @param $fieldScheme
     * @param null $value
     * @param string $comparison
     * @return Model_Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo need refactoring. Not work.
     * @version 0.0
     * @since 0.0
     */
    public function filter($fieldScheme, $value = null, $comparison = '=')
    {
        if (!is_array($fieldScheme)) {
            return $this->filter([[$fieldScheme, $value, $comparison]]);
        }

        if (!is_array(reset($fieldScheme))) {
            return $this->filter([$fieldScheme]);
        }

        $modelClass = $this->_queryResult->getModelClass();
        $collection = $modelClass::getCollection();
        $collection->setData($this->getQueryResult()->filter($fieldScheme));
        return $collection;
    }
}