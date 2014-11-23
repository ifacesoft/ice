<?php
/**
 * Ice core model collection class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core\Model;

use Ice\Core\Data;
use Ice\Core\Exception;
use Ice\Core\Model;
use Ice\Core\Model\Collection\Iterator;
use Ice\Core\Query;
use Ice\Core\Query_Builder;
use IteratorAggregate;
use Traversable;

/**
 * Class Collection
 *
 * Core model collection class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.1
 * @since 0.0
 */
class Collection implements IteratorAggregate
{
    /**
     * Data of model collection
     *
     * @var Data
     */
    private $_data = null;

    /**
     * Private constructor for model collection
     *
     * @param Data $data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    private function __construct(Data $data)
    {
        $this->_data = $data;
    }

    /**
     * Create new instance of model collection
     *
     * @param Data $data
     * @return Collection
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public static function create(Data $data)
    {
        return new Collection($data);
    }

    /**
     * Return size (count potential models) of model collection
     *
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public function getCount()
    {
        return $this->getData()->count();
    }

    /**
     * Return raw data
     *
     * @param string $fieldNames
     * @return Data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getData($fieldNames = '*')
    {
        if ($this->_data !== null) {
            return $this->_data;
        }

        $this->_data = $this->getQueryBuilder()->select($fieldNames)->getQuery()->getData();

        return $this->_data;
    }

    /**
     * Set raw data
     *
     * @deprecated
     * @param Data $data
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function setData(Data $data)
    {
        $this->_data = $data;
    }

    /**
     * Return query builder of model collection
     *
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getQueryBuilder()
    {
        if ($this->_queryBuilder !== null) {
            return $this->_queryBuilder;
        }

        if ($this->_data !== null) {
            return null;
        }

        /** @var Model $modelClass */
        $modelClass = $this->_modelClass;

        $this->_queryBuilder = $modelClass::getQueryBuilder();
        return $this->_queryBuilder;
    }

    /**
     * Return first model of model collection if not empty
     *
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function first()
    {
        $row = $this->getRow();

        if (!$row) {
            return null;
        }

        /** @var Model $modelClass */
        $modelClass = $this->_modelClass;

        return $modelClass::create($row);
    }

    /**
     * Return row from data in model collection
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
        return $this->getData()->getRow($pk);
    }

    /**
     * Add model to model collection
     *
     * @param Model $model
     * @return Collection
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function add(Model $model)
    {
        if ($this->_queryBuilder !== null) {
            throw new Exception('В коллекцию, созданную запросом нельзя добавить свой элемент');
        }

        if ($this->_data === null) {
            $this->setData(new Data([DATA::RESULT_MODEL_CLASS => $this->_modelClass]));
        }

        $this->getData()->setRow($model->getPk(), $model->get());

        return $this;
    }

    /**
     * Insert rows-models to data source
     *
     * @param string|null $sourceName
     * @return Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function insert($sourceName = null)
    {
        $modelClass = $this->_modelClass;

        $this->setData(
            $modelClass::getQueryBuilder()
                ->insert($this->getData()->getRows())
                ->getQuery($sourceName)
                ->getData()
        );

        return $this;
    }

    /**
     * Update rows-models in data source
     *
     * @param $updates
     * @param null $sourceName
     * @return $this
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function update($updates, $sourceName = null)
    {
        if (empty($updates)) {
            return $this;
        }

        /** @var Model $modelClass */
        $modelClass = $this->_modelClass;
        $keys = $this->getKeys();

        $queryBuilder = $modelClass::getQueryBuilder()->update($updates);

        if (count($keys) == 1) {
            $queryBuilder->eq('/pk', reset($keys));
        } else {
            $queryBuilder->in('/pk', $keys);
        }

        $this->setData($queryBuilder->getQuery($sourceName)->getData());

        return $this;
    }

    /**
     * Return primary keys of all models in model collection
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
        return $this->getData()->getKeys();
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
        return new Iterator($this->getData());
    }

    /**
     * Receive model from model collection by primary key
     *
     * @param $pk
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function get($pk)
    {
        $row = $this->getRow($pk);

        if (!$row) {
            return null;
        }

        /** @var Model $modelClass */
        $modelClass = $this->_modelClass;

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
     * @version 0.0
     * @since 0.0
     * @deprecated 0.0 Not work. ERROR: Sql query is empty ;)
     */
    public function remove($pk = null)
    {
        /** @var Model $modelClass */
        $modelClass = $this->_modelClass;

        return $modelClass::create($this->getData()->delete($pk));
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
     * @param string $comparsion
     * @return Collection
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function filter($fieldScheme, $value = null, $comparsion = '=')
    {
        if (!is_array($fieldScheme)) {
            return $this->filter([[$fieldScheme, $value, $comparsion]]);
        }

        if (!is_array(reset($fieldScheme))) {
            return $this->filter([$fieldScheme]);
        }

        /** @var Model $modelClass */
        $modelClass = $this->_modelClass;
        $collection = $modelClass::getCollection();
        $collection->setData($this->getData()->filter($fieldScheme));
        return $collection;
    }
}