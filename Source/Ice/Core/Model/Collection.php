<?php
/**
 * Ice core model collection class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Countable;
use Ice\Core;
use Ice\Helper\Arrays;
use IteratorAggregate;

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
 * @version 0.4
 * @since 0.0
 */
class Model_Collection implements IteratorAggregate, Countable
{
    use Core;

    /**
     * Collection data iterator
     *
     * @var Model_Collection_Iterator
     */
    private $_iterator = null;

    private $_query = null;

    private $_pagination = null;

    /**
     * Private constructor for model collection
     *
     * @param $modelClass
     * @param array $rows
     * @param Query $query
     * @param array $pagination
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    private function __construct($modelClass, array $rows = [], Query $query = null, array $pagination = [1, 1000, 0])
    {
        $this->_iterator = Model_Collection_Iterator::create($modelClass, $rows);
        $this->_query = $query;
        $this->_pagination = $pagination;
    }

    /**
     * Create new instance of model collection
     *
     * @param Model $modelClass
     * @param mixed $data
     * @return Model_Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public static function create($modelClass, $data = null)
    {
        if (is_array($data)) {
            return new Model_Collection($modelClass, $data);
        }

        return $data && $data instanceof Query_Result
            ? new Model_Collection(
                $modelClass,
                $data->getRows(),
                $data->getQuery(),
                $data->getPagination()
            )
            : new Model_Collection($modelClass);
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
//        $this->_queryBuilder = $modelClass::query();
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
        if ($this->_iterator === null) {
            return null;
        }

        $row = $this->_iterator->getRow();

        if (!$row) {
            return null;
        }

        $modelClass = $this->_iterator->getModelClass();

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
     * @version 0.4
     * @since 0.0
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
     * Return collection as array
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
        return $this->getIterator()->getRows();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Model_Collection_Iterator An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function getIterator()
    {
        return $this->_iterator;
    }

    /**
     * Add data to model collection
     *
     * @param Model|Model_Collection|array $data
     * @return Model_Collection
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function add($data)
    {
        $modelClass = $this->getModelClass();

        if ($data instanceof Model) {
            if (!($data instanceof $modelClass)) {
                Model_Collection::getLogger()->fatal(['Add model {$0} to collection of model {$1} failure: type mismatch', [get_class($data), $modelClass]], __FILE__, __LINE__);
            }

            $data = $data->get();
        }

        if ($data instanceof Model_Collection) {
            $modelClass2 = $data->getModelClass();

            if ($modelClass != $modelClass2) {
                Model_Collection::getLogger()->fatal(['Add collection of model {$0} to collection of model {$1} failure: type mismatch', [$modelClass2, $modelClass]], __FILE__, __LINE__);
            }

            $data = $data->getRows();
        }

        if (!is_array($data)) {
            Model_Collection::getLogger()->fatal('Data mast by Model, Model_Collection or array type', __FILE__, __LINE__, null, $data);

        }

        $this->_query = null;
        $this->_pagination = null;

        $this->getIterator()->add($data);

        return $this;
    }

    /**
     * Return model class of collection
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
        return $this->getIterator()->getModelClass();
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
            $modelClass::query()
                ->insert($this->getIterator()->getRows(), false, $sourceName)
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

        $queryBuilder = $modelClass::query();

        if (count($keys) == 1) {
            $queryBuilder->eq(['/pk' => reset($keys)]);
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
        if ($this->_iterator === null) {
            return [];
        }

        return $this->_iterator->getKeys();
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
        $row = $this->_iterator->getRow($pk);

        if (!$row) {
            return null;
        }

        $modelClass = $this->_iterator->getModelClass();

        return $modelClass::create($row);
    }

    /**
     * Remove from data source
     *
     * @param null $sourceName
     * @return Model
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo need refactoring. Not work. ERROR: Sql query is empty ;)
     * @version 0.0
     * @since 0.0
     */
    public function remove($sourceName = null)
    {
        $modelClass = $this->getModelClass();

        $pkFieldNames = $modelClass::getPkFieldNames();

        $this->getIterator()->setRows($modelClass::query()->delete(Arrays::column($this->getRows(), reset($pkFieldNames)), $sourceName)->getRows());
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

        $modelClass = $this->_iterator->getModelClass();
        $collection = $modelClass::getCollection();
        $collection->setData($this->getIterator()->filter($fieldScheme));
        return $collection;
    }

    /**
     * Return of size collection
     *
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
     * @version 0.4
     * @since 0.4
     */
    public function count()
    {
        return count($this->getRows());
    }

    /**
     * Insert or update collection
     *
     * @param null $sourceName
     * @param bool $update
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function save($sourceName = null, $update = false)
    {
        $modelClass = $this->getModelClass();
        $this->getIterator()->setRows($modelClass::query()->insert($this->getRows(), $update, $sourceName)->getRows());
    }
}