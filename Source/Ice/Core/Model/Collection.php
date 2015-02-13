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

    private $_modelClass = null;

    private $_rows = [];

    private $_query = null;

    /**
     * Private constructor for model collection
     *
     * @param $modelClass
     * @param array $rows
     * @param Query $query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    private function __construct($modelClass, array $rows = [], Query $query = null)
    {
        $this->_modelClass = $modelClass;
        $this->_rows = $rows;
        $this->_query = $query;
    }

    /**
     * Create new instance of model collection
     *
     * @param Model $modelClass
     * @param array $rows
     * @param Query $query
     * @return Model_Collection
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public static function create($modelClass, array $rows = [], Query $query = null)
    {
        return new Model_Collection($modelClass, $rows, $query);
    }

    /**
     * Return first model of model collection if not empty
     *
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function first()
    {
        if (!$this->count()) {
            return null;
        }

        $modelClass = $this->getModelClass();

        return $modelClass::create(reset($this->_rows))->clearAffected();
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
        return count($this->_rows);
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
        return $this->_modelClass;
    }

    /**
     * Return last model of model collection if not empty
     *
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function last()
    {
        if (!$this->count()) {
            return null;
        }

        $modelClass = $this->getModelClass();

        return $modelClass::create(end($this->_rows))->clearAffected();
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
                Model_Collection::getLogger()->exception(['Add model {$0} to collection of model {$1} failure: type mismatch', [get_class($data), $modelClass]], __FILE__, __LINE__);
            }

            $data = $data->get();
        }

        if ($data instanceof Model_Collection) {
            $modelClass2 = $data->getModelClass();

            if ($modelClass != $modelClass2) {
                Model_Collection::getLogger()->exception(['Add collection of model {$0} to collection of model {$1} failure: type mismatch', [$modelClass2, $modelClass]], __FILE__, __LINE__);
            }

            $data = $data->getRows();
        }

        if (!is_array($data)) {
            Model_Collection::getLogger()->exception('Data mast by Model, Model_Collection or array type', __FILE__, __LINE__, null, $data);

        }

        if (empty($data)) {
            return;
        }

        if (!is_numeric(each($data)[0])) {
            $data = [$data];
        }

        $pkFieldName = $modelClass::getPkFieldName();

        foreach ($data as $row) {
            $newRow = [];
            foreach ($row as $fieldName => $fieldValue) {
                $newRow[$modelClass::getFieldName($fieldName)] = $fieldValue;
            }
            unset($row);

            $this->_rows[$newRow[$pkFieldName]] = $newRow;
        }

        $this->_query = null;

        return $this;
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
        return $this->_rows;
    }

    /**
     * Return primary keys of all models in model collection
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function getKeys()
    {
        if (!$this->count()) {
            return null;
        }

        $modelClass = $this->getModelClass();

        return Arrays::column($this->getRows(), $modelClass::getPkFieldNames(), '');
    }

    /**
     * Receive model from model collection by primary key
     *
     * @param $pk
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function get($pk)
    {
        if (!$this->count()) {
            return null;
        }

        $modelClass = $this->getModelClass();

        return $modelClass::create($this->getRow($pk))->clearAffected();
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
        if (empty($this->_rows)) {
            return null;
        }

        if (isset($pk)) {
            return isset($this->_rows[$pk]) ? $this->_rows[$pk] : null;
        }

        return reset($this->_rows);
    }

    /**
     * Remove from data source
     *
     * @param string|null $dataSourceKey
     * @return Model_Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function remove($dataSourceKey = null)
    {
        $modelClass = $this->getModelClass();

        $pkFieldNames = $modelClass::getPkFieldNames();

        $this->_rows = $modelClass::query()
            ->delete(Arrays::column($this->getRows(), reset($pkFieldNames)), $dataSourceKey)
            ->getRows();

        $this->_query = null;
    }

//    /**
//     * Filter model collection by filterScheme
//     *
//     * example filter scheme:
//     * ```php
//     *      $filterScheme = [
//     *          ['name', 'Petya', '='],
//     *          ['age', 18, '>'],
//     *          ['surname', 'Iv%', 'like']
//     *      ];
//     * ```
//     *
//     * example usage:
//     * ```php
//     *  ->filter('name', 'Petya')
//     *  ->filter(['age', 18, '>'])
//     *  ->filter([['surname', 'Iv%', 'like']])
//     * ```
//     *
//     * @see Arrays::filter()
//     *
//     * @param $fieldScheme
//     * @param null $value
//     * @param string $comparison
//     * @return Model_Collection
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @todo need refactoring. Not work.
//     * @version 0.0
//     * @since 0.0
//     */
//    public function filter($fieldScheme, $value = null, $comparison = '=')
//    {
//        if (!is_array($fieldScheme)) {
//            return $this->filter([[$fieldScheme, $value, $comparison]]);
//        }
//
//        if (!is_array(reset($fieldScheme))) {
//            return $this->filter([$fieldScheme]);
//        }
//
//        $modelClass = $this->_iterator->getModelClass();
//        $collection = $modelClass::getCollection();
//        $collection->setData($this->_rows->filter($fieldScheme));
//        return $collection;
//    }

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
        return Model_Collection_Iterator::create($this->getModelClass(), $this->getRows());
    }

    /**
     * Insert or update collection
     *
     * @param string|null $dataSourceKey
     * @param bool $update
     * @return Model_Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function save($dataSourceKey = null, $update = false)
    {
        $modelClass = $this->getModelClass();
        $this->_rows = $modelClass::query()
            ->insert($this->getRows(), $update, $dataSourceKey)
            ->getRows();

        $this->_query = null;

        return $this;
    }

    /**
     * Reload collection if query known
     *
     * @return Model_Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function reload()
    {
        if (!$this->_query) {
            Model_Collection::getLogger()->exception('Model collection is artificial', __FILE__, __LINE__);
        }

        $this->_rows = $this->_query->execute()->getRows();
        return $this;
    }

    /**
     * Return query of model collection
     *
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * Return column from model collection
     *
     * @param null $columnKey
     * @param null $indexKey
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function column($columnKey = null, $indexKey = null)
    {
        return Arrays::column($this->getRows(), $columnKey, $indexKey);
    }
}