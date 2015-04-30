<?php
/**
 * Ice core query class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Arrays;
use Ice\Helper\Json;
use Ice\Widget\Data\Table;
use Ice\Widget\Form\Simple;
use Ice\Widget\Menu\Pagination;

/**
 * Class Query
 *
 * Core query class
 *
 * @see Ice\Core\Container
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
     * @var Query_Builder
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
     * @param  Query_Builder $queryBuilder
     * @param  $dataSourceKey
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo    Need caching
     * @version 0.6
     * @since   0.0
     */
    public static function create(Query_Builder $queryBuilder, $dataSourceKey)
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
     * @return Query_Builder
     */
    public static function getBuilder($modelClass, $tableAlias = null)
    {
        return Query_Builder::create($modelClass, $tableAlias);
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
        $selectQueryParts = $this->queryBuilder->getSqlParts(Query_Builder::PART_SELECT);
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

        if ($this->queryBuilder->getQueryType() == Query_Builder::TYPE_SELECT) {
            $this->bindHash = md5(json_encode($bindParts));
        }

        return $this;
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
     */
    public function getFullHash()
    {
        if ($this->bindHash === null) {
            Query::getLogger()->exception('Bind hash is empty', __FILE__, __LINE__, null, $this);
        }

        return $this->hash . '/' . $this->bindHash;
    }

    public function getBody()
    {
        $queryTranslatorClass = $this->getDataSource()->getQueryTranslatorClass();
        return $queryTranslatorClass::getInstance()->translate($this->getBodyParts());
    }

    /**
     * Return data source name
     *
     * @return Data_Source
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public function getDataSource()
    {
        return Data_Source::getInstance($this->getDataSourceKey());
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
     * Return query body parts
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function getBodyParts()
    {
        return $this->queryBuilder->getSqlParts();
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
     * @param  int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo    костылькостылем погоняет((
     * @version 0.6
     * @since   0.4
     */
    public function getQueryResult($ttl = null)
    {
        $queryResult = $this->getDataSource()->execute($this, $ttl);

        $params = [];

        if (isset($this->queryBuilder->getUis()[Simple::getClass()])) {
            foreach ($this->queryBuilder->getUis()[Simple::getClass()] as $ui) {
                $params += (array)$ui->getValues();
            }
        }

        if (isset($this->queryBuilder->getUis()[Pagination::getClass()])) {
            foreach ($this->queryBuilder->getUis()[Pagination::getClass()] as $ui) {
                $params += $ui->getValues();
            }
        }

        if (isset($this->queryBuilder->getUis()[Table::getClass()])) {
            foreach ($this->queryBuilder->getUis()[Table::getClass()] as $ui) {
                foreach ($ui->getValues() as $key => $value) {
                    if ($value) {
                        if (!isset($params[$key])) {
                            $params[$key] = $value;
                        } else {
                            $params[$key] .= '/' . $value;
                        }
                    }
                }
            }
        }

        foreach ($this->queryBuilder->getUis() as $uis) {
            foreach ($uis as $ui) {
                $ui->setParams($params);
                $ui->setQueryResult($queryResult);
            }
        }

        return $queryResult;
    }

    /**
     * @return Query_Builder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * Get value from data
     *
     * @desc Результат запроса - единственное значение.
     *
     * @param  null $columnName
     * @param  null $ttl
     * @return mixed
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getValue($columnName = null, $ttl = null)
    {
        $row = $this->getRow(null, $ttl);
        return $row ? ($columnName ? $row[$columnName] : reset($row)) : null;
    }

    /**
     * Get first row from data
     *
     * @desc Результат запроса - единственная запись таблицы.
     *
     * @param  null $pk
     * @param  null $ttl
     * @return array|null
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
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getRows($ttl = null)
    {
        return $this->getQueryResult($ttl)->getRows();
    }

    /**
     * Return model from data
     *
     * @param  null $pk
     * @param  null $ttl
     * @return Model|null
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getModel($pk = null, $ttl = null)
    {
        $row = $this->getRow($pk, $ttl);

        if (empty($row)) {
            return null;
        }

        $modelClass = $this->queryBuilder->getModelClass();

        return $modelClass::create($row)->clearAffected();
    }

    /**
     * Return column in data
     *
     * @param  null $fieldName
     * @param  null $indexKey
     * @param  null $ttl
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getColumn($fieldName = null, $indexKey = null, $ttl = null)
    {
        return empty($fieldName)
            ? $this->getKeys()
            : Arrays::column($this->getRows($ttl), $fieldName, $indexKey);
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
     */
    public function getKeys($ttl = null)
    {
        return array_keys($this->getRows($ttl));
    }
}
