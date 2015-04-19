<?php
/**
 * Ice core model collection class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
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
 * @package    Ice
 * @subpackage Core
 *
 * @version 0.4
 * @since   0.0
 */
class Model_Collection implements IteratorAggregate, Countable
{
    use Core;

    private $modelClass = null;

    private $rows = [];

    /**
     * Private constructor for model collection
     *
     * @param $modelClass
     * @param array $rows
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    private function __construct($modelClass, array $rows = [])
    {
        $this->modelClass = $modelClass;
        $this->rows = $rows;
    }

    /**
     * Create new instance of model collection
     *
     * @param  Model $modelClass
     * @param  array $rows
     * @return Model_Collection
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public static function create($modelClass, array $rows = [])
    {
        return new Model_Collection($modelClass, $rows);
    }

    /**
     * Return first model of model collection if not empty
     *
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function first()
    {
        if (!$this->count()) {
            return null;
        }

        $modelClass = $this->getModelClass();

        return $modelClass::create(reset($this->rows))->clearAffected();
    }

    /**
     * Return of size collection
     *
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
     * @version 0.4
     * @since   0.4
     */
    public function count()
    {
        return count($this->rows);
    }

    /**
     * Return model class of collection
     *
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * Return last model of model collection if not empty
     *
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function last()
    {
        if (!$this->count()) {
            return null;
        }

        $modelClass = $this->getModelClass();

        return $modelClass::create(end($this->rows))->clearAffected();
    }

    /**
     * Add data to model collection
     *
     * @param  Model|Model_Collection|array $data
     * @return Model_Collection
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function add($data)
    {
        $modelClass = $this->getModelClass();

        if ($data instanceof Model) {
            if (!($data instanceof $modelClass)) {
                Model_Collection::getLogger()->exception(
                    [
                        'Add model {$0} to collection of model {$1} failure: type mismatch',
                        [get_class($data), $modelClass]
                    ],
                    __FILE__,
                    __LINE__
                );
            }

            $data = $data->get();
        }

        if ($data instanceof Model_Collection) {
            $modelClass2 = $data->getModelClass();

            if ($modelClass != $modelClass2) {
                Model_Collection::getLogger()->exception(
                    [
                        'Add collection of model {$0} to collection of model {$1} failure: type mismatch',
                        [$modelClass2, $modelClass]
                    ],
                    __FILE__,
                    __LINE__
                );
            }

            $data = $data->getRows();
        }

        if (!is_array($data)) {
            Model_Collection::getLogger()->exception(
                'Data mast by Model, Model_Collection or array type',
                __FILE__,
                __LINE__,
                null,
                $data
            );
        }

        if (empty($data)) {
            return $this;
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

            $this->rows[$newRow[$pkFieldName]] = $newRow;
        }

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
     * @since   0.4
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Receive model from model collection by primary key
     *
     * @param  $pk
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
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
     * @param  mixed $pk
     * @return array|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function getRow($pk = null)
    {
        if (empty($this->rows)) {
            return null;
        }

        if (isset($pk)) {
            return isset($this->rows[$pk]) ? $this->rows[$pk] : null;
        }

        return reset($this->rows);
    }

    /**
     * Remove from data source
     *
     * @param  string|null $dataSourceKey
     * @return Model_Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function remove($dataSourceKey = null)
    {
        $modelClass = $this->getModelClass();

        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

        $this->rows = Query::getBuilder($modelClass)
            ->deleteQuery(Arrays::column($this->getRows(), reset($pkFieldNames)), $dataSourceKey)
            ->getRows();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     *
     * @link   http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Model_Collection_Iterator An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function getIterator()
    {
        return Model_Collection_Iterator::create($this->getModelClass(), $this->getRows());
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
     * Insert or update collection
     *
     * @param  string|null $dataSourceKey
     * @param  bool $update
     * @return Model_Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function save($dataSourceKey = null, $update = false)
    {
        $modelClass = $this->getModelClass();
        $this->rows = Query::getBuilder($modelClass)
            ->insertQuery($this->getRows(), $update, $dataSourceKey)
            ->getRows();

        return $this;
    }

    /**
     * Return column from model collection
     *
     * @param  null $columnKey
     * @param  null $indexKey
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function column($columnKey = null, $indexKey = null)
    {
        return Arrays::column($this->getRows(), $columnKey, $indexKey);
    }

    /**
     * Get query builder from model collection
     *
     * @param  null $modelClass
     * @param  null $tableAlias
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
     */
    public function getQueryBuilder($modelClass = null, $tableAlias = null)
    {
        return $modelClass
            ? Query::getBuilder($modelClass, $tableAlias)
                ->inner($this->modelClass)
                ->inPk($this->getKeys(), $this->modelClass)
            : Query::getBuilder($this->modelClass)
                ->inPk($this->getKeys());
    }

    /**
     * Return primary keys of all models in model collection
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function getKeys()
    {
        if (!$this->count()) {
            return [];
        }

        $modelClass = $this->getModelClass();

        return Arrays::column($this->getRows(), $modelClass::getScheme()->getPkFieldNames(), '');
    }
}
