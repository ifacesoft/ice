<?php
/**
 * Ice core query class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Helper\Type_Array;
use Ice\Helper\Json;
use Throwable;

/**
 * Class Query
 *
 * Core query class
 *
 * @see \Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Query
{
    use Stored;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder = null;

    /**
     * Bind parts
     *
     * @var array
     */
    private $bindParts = [];

    /**
     * Data source name
     *
     * @var string
     */
    private $dataSourceKey = null;

    /**
     * Query sql md5 hash
     *
     * @var string
     */
    private $hash = null;

    /**
     * Serialized bind values
     *
     * @var string
     */
    private $bindHash = null;

    /**
     * Private constructor of query builder. Create: Query::create()->...
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    private function __construct()
    {
    }

    /**
     * Create new instance of query
     *
     * @param  QueryBuilder $queryBuilder
     * @param  $dataSourceKey
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo    Need caching
     * @version 0.6
     * @since   0.0
     * @throws \Exception
     */
    public static function create(QueryBuilder $queryBuilder, $dataSourceKey)
    {
        $query = new Query();

        $query->queryBuilder = $queryBuilder;

        $modelClass = $queryBuilder->getModelClass();

        if (!$dataSourceKey) {
            $dataSourceKey = $modelClass::getDataSourceKey();
        }

        $query->dataSourceKey = $dataSourceKey;

        $query->hash = md5(
            Json::encode(
                [
                    $queryBuilder->getQueryType(),
                    $queryBuilder->getSqlParts(),
                    $queryBuilder->getModelClass(),
                    $queryBuilder->getCacheTags(),
                    $queryBuilder->getTriggers(),
                    $queryBuilder->getTransforms()
                ]
            )
        );

        return $query;
    }

    /**
     * @param $modelClass
     * @param $tableAlias
     * @return QueryBuilder
     * @throws Exception
     */
    public static function getBuilder($modelClass, $tableAlias = null)
    {
        return QueryBuilder::create($modelClass, $tableAlias);
    }

    /**
     * Return calc found rows flag
     *
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function isCalcFoundRows()
    {
        $selectQueryParts = $this->queryBuilder->getSqlParts(QueryBuilder::PART_SELECT);
        return reset($selectQueryParts);
    }

    /**
     * Bind values
     *
     * @param  array $bindParts
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.0
     */
    public function bind(array $bindParts)
    {
        $this->bindParts = $bindParts;

        if ($this->queryBuilder->getQueryType() === QueryBuilder::TYPE_SELECT) {
            $this->bindHash = md5(json_encode($bindParts));
        }

        return $this;
    }

    /**
     * Return validate tags
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getValidateTags()
    {
        return $this->queryBuilder->getCacheTags()[Cache::VALIDATE];
    }

    /**
     * Return invalidate tags
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getInvalidateTags()
    {
        return $this->queryBuilder->getCacheTags()[Cache::INVALIDATE];
    }

    /**
     * Return full hash of query
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     * @throws Exception
     */
    public function getFullHash()
    {
        if ($this->bindHash === null) {
            $this->getLogger()->exception('Bind hash is empty', __FILE__, __LINE__, null, $this);
        }

        return $this->hash . '/' . $this->bindHash;
    }

    public function getLogger()
    {
        return Logger::getInstance($this->getModelClass());
    }

    /**
     * @deprecated use $this->getQueryBuilder()->getModelClass()
     * @return Model|string
     */
    public function getModelClass()
    {
        return $this->getQueryBuilder()->getModelClass();
    }

    /**
     * @deprecated use $this->getQueryBuilder()->getTableAlias()
     * @return Model|string
     */
    public function getTableAlias()
    {
        return $this->getQueryBuilder()->getTableAlias();
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    public function getAfterSelectTriggers()
    {
        return $this->queryBuilder->getTriggers()['afterSelect'];
    }

    /**
     * Get collection from data
     *
     * @param  null $ttl
     * @return Model_Collection
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     * @throws \Exception
     */
    public function getModelCollection($ttl = null)
    {
        $queryResult = $this->getQueryResult($ttl);

        return Model_Collection::create(
            $queryResult->getQuery()->getQueryBuilder()->getModelClass(),
            $queryResult->getRows()
        );
    }

    /**
     * Execute query
     *
     * @param null $ttl
     * @param bool|string|array $indexFieldNames
     * @return QueryResult
     *
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     * @throws Throwable
     * @author dp <denis.a.shestakov@gmail.com>
     * @todo: Реализовать indexKeys (по умолчанию false, true = индекс - первичный ключ.. )
     * @version 1.1
     * @since   0.4
     */
    public function getQueryResult($ttl = null, $indexFieldNames = true)
    {
        /** @var DataSource $dataSource */
        $dataSource = $this->getDataSource();

        $queryResult = $dataSource->executeQuery($this, $ttl, $indexFieldNames);

        foreach ($this->queryBuilder->getWidgets() as $widget) {
            $widget->setQueryResult($queryResult);
        }

        return $queryResult;
    }

    /**
     * Return data source name
     *
     * @return \Ice\Core|Container
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     * @throws Exception
     */
    public function getDataSource()
    {
        return DataSource::getInstance($this->getDataSourceKey());
    }

    /**
     * Return data source key of query
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getDataSourceKey()
    {
        return $this->dataSourceKey;
    }

    /**
     * Get value from data
     *
     * @desc Результат запроса - единственное значение.
     *
     * @param null|string $fieldName
     * @param  null $ttl
     * @param null $default
     * @return mixed
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.14
     * @since   0.0
     * @throws \Exception
     */
    public function getValue($fieldName = null, $ttl = null, $default = null)
    {
        $row = $this->getRow(null, $ttl);

        if ($fieldName) {
            $modelClass = $this->getQueryBuilder()->getModelClass();

            $fieldName = $modelClass::getFieldName($fieldName);
        }

        return $row
            ? ($fieldName ? $row[$fieldName] : reset($row))
            : $default;
    }

    /**
     * Get first row from data
     *
     * @desc Результат запроса - единственная запись таблицы.
     *
     * @param null $pk
     * @param null $ttl
     * @return array|null
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getRow($pk = null, $ttl = null)
    {
        $rows = $this->getRows($ttl);

        if (empty($rows)) {
            return null;
        }

        if (isset($pk)) {
            return isset($rows[$pk]) ? $rows[$pk] : null;
        }

        return reset($rows);
    }

    /**
     * Return all rows from data as array
     *
     * @param  null $ttl
     * @param bool $indexFieldNames
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getRows($ttl = null, $indexFieldNames = true)
    {
        return $this->getQueryResult($ttl, $indexFieldNames)->getRows();
    }

    /**
     * Return model from data
     *
     * @param null $modelCLass
     * @param null $ttl
     * @return Model|null
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getModel($modelCLass = null, $ttl = null)
    {
        $row = $this->getRow(null, $ttl);

        if (empty($row)) {
            return null;
        }

        $modelClass = $modelCLass ? $modelCLass : $this->queryBuilder->getModelClass();

        return $modelClass::create($row)->clearAffected();
    }

    /**
     * Return column in data
     *
     * @param null|string $fieldName
     * @param  null $indexKey
     * @param  null $ttl
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   0.0
     * @throws \Exception
     */
    public function getColumn($fieldName = null, $indexKey = null, $ttl = null)
    {
        $modelClass = $this->getQueryBuilder()->getModelClass();

        if ($fieldName) {
            $fieldName = (array)$fieldName;
            foreach ($fieldName as &$name) {
                $name = $modelClass::getFieldName($name);
            }
        }

        if ($indexKey) {
            if (\is_array($indexKey)) {
                foreach ($indexKey as &$key) {
                    $key = $modelClass::getFieldName($key);
                }
            } else {
                $indexKey = $modelClass::getFieldName($indexKey);
            }
        }

        $rows = $this->getRows($ttl); // todo: должен быть ArrayObject чтобы передавался по ссылке в ::column

        if (!$rows) {
            return [];
        }

        if (!$fieldName) {
            $row = reset($rows);

            reset($row);

            $fieldName = key($row);
        }

        return Type_Array::column($rows, $fieldName, $indexKey);
    }

    /**
     * Return keys of data
     *
     * @param  null $ttl
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     * @throws \Exception
     */
    public function getKeys($ttl = null)
    {
        return array_keys($this->getRows($ttl));
    }

    public function dumpQuery($die = false)
    {
        Debuger::dump([$this->getBody(), Json::encode($this->getBinds())]);

        if ($die) {
            die();
        }

        return $this;
    }

    /**
     * @param DataSource|null $dataSource
     * @return mixed
     * @throws Exception
     * @throws \Exception
     */
    public function getBody(DataSource $dataSource = null)
    {
        if (!$dataSource) {
            $dataSource = $this->getDataSource();
        }

        $repository = Query::getRepository($dataSource->getDataSourceKey());

        $key = 'body_' . md5(Json::encode([$this->getQueryBuilder()->getSqlParts(), [$this->getModelClass(), $this->getTableAlias()]]));

        //TODO: cache turned off
//        if ($queryString = $repository->get($key)) {
//            return $queryString;
//        }

        return $repository->set([$key => $dataSource->translate($this)])[$key];
    }

    /**
     * Get bind params
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
     */
    public function getBinds()
    {
        $binds = [];

        foreach ($this->getBindParts() as $bindPart) {
            if (!is_array(reset($bindPart))) {
                $binds = array_merge($binds, array_values($bindPart));
                continue;
            }

            foreach ($bindPart as $values) {
                $binds = array_merge($binds, array_values($values));
                continue;
            }
        }

        return $binds;
    }

    /**
     * Return rows
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getBindParts()
    {
        return $this->bindParts;
    }

    /**
     * @param $columnFieldNames
     * @param array|null $groups
     * @param null $indexFieldNames
     * @param null $ttl
     * @param null $indexGroupFieldNames
     * @param array $aggregate
     * @param array $exclude
     * @return array
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     * @throws Throwable
     * @TODO отсортировать аргументы
     */
    public function getGroup($columnFieldNames, array $groups = null, $indexFieldNames = null, $ttl = null, $indexGroupFieldNames = null, array $aggregate = [], array $exclude = [])
    {
        return $this->getQueryResult($ttl, false)->getGroup($columnFieldNames, $groups, $indexFieldNames, $indexGroupFieldNames, $aggregate, $exclude);
    }

    /**
     * Научиться Делать запросы в режиме билдера каскаюно..
     *
     * MyModel::query(['id'], 'MyMode1')
     *      ->where('active=1')
     *      ->fromQuery(['id', 'some'], 'ActiveMyModel')
     *      ->where('some=1')
     *      ->getRows();
     *
     * select id from (select id from my_model MyModel1 where active=1) ActiveMyModel where some=1
     *
     * @param $tableAlias
     */
    public function fromQuery(array $fieldNames, $tableAlias)
    {

    }
}
