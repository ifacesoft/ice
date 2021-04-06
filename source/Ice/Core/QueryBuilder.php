<?php
/**
 * Ice core query builder class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Exception\QueryBuilder_Join;
use Ice\Helper\Class_Object;
use Ice\Widget\Form;

/**
 * Class QueryBuilder
 *
 * Core query builder class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class QueryBuilder
{
    use Stored;

    const TYPE_CREATE = 'create';
    const TYPE_DROP = 'drop';
    const TYPE_SELECT = 'select';
    const TYPE_INSERT = 'insert';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';
    const PART_CREATE = 'create';
    const PART_DROP = 'drop';
    const PART_SELECT = 'select';
    const PART_VALUES = 'values';
    const PART_SET = 'set';
    const PART_JOIN = 'join';
    const PART_WHERE = 'where';
    const PART_GROUP = 'group';
    const PART_HAVING = 'having';
    const PART_ORDER = 'order';
    const PART_LIMIT = 'limit';
    const DEFAULT_PART_CREATE = [];
    const DEFAULT_PART_DROP = [];
    const DEFAULT_PART_SELECT = ['_calcFoundRows' => null, '_distinct' => null, '_sqlNoCache' => null];
    const DEFAULT_PART_VALUES = ['_update' => null];
    const DEFAULT_PART_JOIN = [];
    const DEFAULT_PART_SET = [];
    const DEFAULT_PART_WHERE = ['_delete' => null];
    const DEFAULT_PART_GROUP = [];
    const DEFAULT_PART_HAVING = [];
    const DEFAULT_PART_ORDER = [];
    const DEFAULT_PART_LIMIT = ['offset' => 0, 'limit' => 0];
    const SQL_CLAUSE_INNER_JOIN = 'INNER JOIN';
    const SQL_CLAUSE_LEFT_JOIN = 'LEFT JOIN';
    const SQL_CLAUSE_RIGHT_JOIN = 'RIGHT JOIN';
    const SQL_CLAUSE_KEYWORD_JOIN = 'JOIN';
    const SQL_LOGICAL_AND = 'AND';
    const SQL_LOGICAL_OR = 'OR';
    const SQL_LOGICAL_NOT = 'NOT';
    const SQL_COMPARISON_OPERATOR_RAW = 'raw';
    const SQL_COMPARISON_OPERATOR_EQUAL = '=';
    const SQL_COMPARISON_OPERATOR_NOT_EQUAL = '<>';
    const SQL_COMPARISON_OPERATOR_LESS = '<';
    const SQL_COMPARISON_OPERATOR_GREATER = '>';
    const SQL_COMPARISON_OPERATOR_GREATER_OR_EQUAL = '>=';
    const SQL_COMPARISON_OPERATOR_LESS_OR_EQUAL = '<=';
    const SQL_COMPARISON_KEYWORD_REGEXP = 'REGEXP';
    const SQL_COMPARISON_KEYWORD_LIKE = 'LIKE';
    const SQL_COMPARISON_KEYWORD_RLIKE = 'RLIKE';
    const SQL_COMPARISON_KEYWORD_RLIKE_REVERSE = 'RLIKE_REVERSE';
    const SQL_COMPARISON_KEYWORD_IN = 'IN';
    const SQL_COMPARISON_KEYWORD_NOT_IN = 'NOT IN';
    const SQL_COMPARISON_KEYWORD_BETWEEN = 'BETWEEN';
    const SQL_COMPARISON_KEYWORD_IS_NULL = 'IS NULL';
    const SQL_COMPARISON_KEYWORD_IS_NOT_NULL = 'IS NOT NULL';
    const SQL_ORDERING_ASC = 'ASC';
    const SQL_ORDERING_DESC = 'DESC';
    const SQL_ORDERING_RAND = 'RAND()';
    const SEARCH_KEYWORD = '$search';
    const DEFAULT_PAGINATION_PAGE = 1;
    const DEFAULT_PAGINATION_LIMIT = 10;

    /**
     * Main model class for builded query
     *
     * @access private
     * @var    Model
     */
    private $modelClass = null;

    /**
     * Table alias for prefix column in query
     *
     * @access private
     * @var    null|string
     */
    private $tableAlias = null;

    /**
     * Query statement type (SELECT|INSERT|UPDATE|DELETE)
     *
     * @access private
     * @var    string
     */
    private $queryType = null;

    /**
     * Query parts
     *
     * @access private
     * @var    array
     */
    private $sqlParts = [
        QueryBuilder::PART_CREATE => QueryBuilder::DEFAULT_PART_CREATE,
        QueryBuilder::PART_DROP => QueryBuilder::DEFAULT_PART_DROP,
        QueryBuilder::PART_SELECT => QueryBuilder::DEFAULT_PART_SELECT,
        QueryBuilder::PART_VALUES => QueryBuilder::DEFAULT_PART_VALUES,
        QueryBuilder::PART_JOIN => QueryBuilder::DEFAULT_PART_JOIN,
        QueryBuilder::PART_SET => QueryBuilder::DEFAULT_PART_SET, // todo: разделить UPDATE и SET (между ними может быть JOIN)
        QueryBuilder::PART_WHERE => QueryBuilder::DEFAULT_PART_WHERE,
        QueryBuilder::PART_GROUP => QueryBuilder::DEFAULT_PART_GROUP,
        QueryBuilder::PART_HAVING => QueryBuilder::DEFAULT_PART_HAVING,
        QueryBuilder::PART_ORDER => QueryBuilder::DEFAULT_PART_ORDER,
        QueryBuilder::PART_LIMIT => QueryBuilder::DEFAULT_PART_LIMIT
    ];

    /**
     * Query binds
     *
     * @access private
     * @var    array
     */
    private $bindParts = [
        self::PART_SELECT => [],
        self::PART_VALUES => [],
        self::PART_SET => [],
        self::PART_WHERE => [],
        self::PART_HAVING => [],
        self::PART_LIMIT => []
    ];

    private $triggers = [
        'beforeSelect' => [],
        'afterSelect' => [],
        'beforeInsert' => [],
        'afterInsert' => [],
        'beforeUpdate' => [],
        'afterUpdate' => [],
        'beforeDelete' => [],
        'afterDelete' => [],
    ];

    /**
     * Query caches tags (validates and invalidates)
     *
     * @access private
     * @var    array
     */
    private $cacheTags = [
        Cache::VALIDATE => [],
        Cache::INVALIDATE => []
    ];

    /**
     * @var Widget[]
     */
    private $widgets = [];

    private $transforms = [];

    /**
     * Private constructor of query builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    private function __construct()
    {
    }

    /**
     * Return new instance of query builder
     *
     * @param string $modelClass Class of model
     * @param string|null $tableAlias Alias of table in query (default: class name of model)
     * @return QueryBuilder
     *
     * @throws Exception
     * @version 0.6
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public static function create($modelClass, $tableAlias = null)
    {
        $queryBuilder = new QueryBuilder();

        $queryBuilder->modelClass = Model::getClass($modelClass);

        if (!$tableAlias) {
            $tableAlias = $modelClass;
        }

        $queryBuilder->tableAlias = Class_Object::getClassName($tableAlias);

        return $queryBuilder;
    }

    /**
     * Set in query part where expression 'IS NOT NULL'
     *
     * @param  $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @version 1.3
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function notNull($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        $eq = [];

        foreach ((array)$fieldName as $fn) {
            $eq[$fn] = null;
        }

        return $this->where(
            $eq,
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set data in query part where
     *
     *  part structure:
     * ```php
     *      $_sqlPart[self::PART_WHERE] = [
     *          $modelClass => [
     *              $tableAlias, [
     *                  [
     *                      Query::CLAUSE_WHERE_LOGICAL_OPERATOR => $sqlLogical,
     *                      Query::CLAUSE_WHERE_FIELD_NAME => $fieldName,
     *                      Query::CLAUSE_WHERE_COMPARISON_OPERATOR => $sql_comparison
     *                  ]
     *              ]
     *          ]
     *      ];
     * ```
     *
     * @param $fieldNameValues
     * @param array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @param string $sqlComparison
     * @param string $sqlLogical
     * @param bool $isUse
     * @param string $part
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo 1.3 Инжектить парт, куда заполняем дату (data) + сгруппировать по классу и алиасу
     *
     * @version 1.9
     * @since   0.0
     */
    public function where($fieldNameValues, $modelTableData = [], $sqlComparison = QueryBuilder::SQL_COMPARISON_OPERATOR_RAW, $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true, $part = QueryBuilder::PART_WHERE)
    {
        if (!$fieldNameValues || !$isUse) {
            return $this;
        }

        /** @var Model $modelClass */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        if (is_string($fieldNameValues)) {
            $this->sqlParts[$part][] = [
                'class' => $modelClass,
                'alias' => $tableAlias,
                'data' => [[$sqlLogical, $fieldNameValues, QueryBuilder::SQL_COMPARISON_OPERATOR_RAW, null]]
            ];

            return $this;
        }

        foreach ($fieldNameValues as $fieldName => $value) {
            if (is_array($value)) {
                if ($sqlComparison === QueryBuilder::SQL_COMPARISON_OPERATOR_EQUAL) {
                    $this->in($fieldName, $value, $modelTableData, $sqlLogical, $isUse);

                    continue;
                }

                if ($sqlComparison === QueryBuilder::SQL_COMPARISON_OPERATOR_NOT_EQUAL) {
                    $this->notIn($fieldName, $value, $modelTableData, $sqlLogical, $isUse);

                    continue;
                }
            }

            $fieldName = $modelClass::getFieldName($fieldName);

            if ($value instanceof Model) {
                $value = $value->getPk();
                $fieldName .= '__fk';
            }

            if ($value instanceof Query) {
                $value = $value->getColumn();
            }

            $comparison = $sqlComparison;


            if ($value === null) {
                if ($comparison !== QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL) {
                    $comparison = QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NULL;
                }

                $valueCount = 1;
            } else {
                $valueCount = is_string($value) && mb_strpos($value, '`') === 0 && mb_substr($value, -1) === '`'
                    ? $value
                    : count((array)$value);
            }

            $where = [$sqlLogical, $fieldName, $comparison, $valueCount];

            $this->sqlParts[$part][] = [
                'class' => $modelClass,
                'alias' => $tableAlias,
                'data' => [$where]
            ];

            $this->appendCacheTag($modelClass, $fieldName, true, false);

            if (!is_string($valueCount)
                && $comparison != QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NULL
                && $comparison != QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL
            ) {
                $this->bindParts[$part][] = $value === null
                    ? [null]
                    : array_values((array)$value);
            }
        }

        return $this;
    }

    /**
     * Check for model class and table alias
     *
     * @param  $modelTableData
     * @return array
     *
     * @throws Exception
     * @version 1.13
     * @since   0.6
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function getModelClassTableAlias($modelTableData)
    {
        if (is_array($modelTableData) && count($modelTableData) === 1 && is_string(key($modelTableData))) {
            Logger::getInstance()->warning('depricated param. Use [value, alias] insteed [value => alias]', __FILE__, __LINE__);
        }

        if (is_array($modelTableData) && count($modelTableData) === 1 && is_int(key($modelTableData))) {
            $modelTableData = reset($modelTableData);
        }

        if (is_object($modelTableData) || (is_array($modelTableData) && count($modelTableData) > 1 && is_object($modelTableData[1]))) {
            $modelTableData = [$modelTableData];
        }

        if (empty($modelTableData)) {
            $modelClass = null;
            $tableAlias = null;
        } else {
            $modelTableData = (array)$modelTableData;

            if (count($modelTableData) > 1) {
                $modelClass = array_shift($modelTableData);
                $tableAlias = array_shift($modelTableData);
            } else {
                // deprecated 7.2
                //list($modelClass, $tableAlias) = each($modelTableData);

                $tableAlias = reset($modelTableData);
                $modelClass = key($modelTableData);
            }

            if (is_int($modelClass)) { //todo: Выяснить зачем это
                $modelClass = $tableAlias;
                $tableAlias = null;
            }
        }

        if ($modelClass) {
            if (!is_object($modelClass) && !is_array($modelClass)) {
                $modelClass = Model::getClass($modelClass);
            }
        } else {
            $modelClass = $this->getModelClass();

            if (!$tableAlias) {
                $tableAlias = $this->getTableAlias();
            }
        }

        if (!$tableAlias) {
            if ($modelClass instanceof Query) {
                $tableAlias = $modelClass->getQueryBuilder()->getTableAlias();
            } else if ($modelClass instanceof QueryBuilder) {
                $tableAlias = $modelClass->getTableAlias();
            } else if (is_array($modelClass)) {
                $tableAlias = Class_Object::getClassName($this->getModelClass());
            } else {
                $tableAlias = Class_Object::getClassName($modelClass);
            }
        }

        return [$modelClass, $tableAlias];
    }

    /**
     * Return model class for query
     *
     * @return Model|string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * Return table alias for model class for query
     *
     * @return Model|string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getTableAlias()
    {
        return $this->tableAlias;
    }

    /**
     * Set in query part where expression 'in (?)'
     *
     * @param  $fieldName
     * @param array $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.13
     * @since   0.0
     */
    public function in($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        if (empty($fieldValue)) {
            $fieldValue = [0];
        }

        return $this->where(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_KEYWORD_IN,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set in query part where expression 'not in (?)'
     *
     * @param  $fieldName
     * @param array $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public function notIn($fieldName, array $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        if (empty($fieldValue)) {
            $fieldValue = [0];
        }

        return $this->where(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_KEYWORD_NOT_IN,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Append cache validate or invalidate tags for this query builder
     *
     * @param Model $modelClass
     * @param $fieldNames
     * @param $isValidate
     * @param $isInvalidate boolean
     *
     * @throws Exception
     * @version 0.0
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    private function appendCacheTag($modelClass, $fieldNames, $isValidate, $isInvalidate)
    {
        $columnFieldMapping = $modelClass::getScheme()->getColumnFieldMap();

        foreach ((array)$fieldNames as $fieldName) {
            if (in_array($fieldName, $columnFieldMapping)) {
                if ($isValidate) {
                    $this->cacheTags[Cache::VALIDATE][$modelClass][$fieldName] = true;
                }
                if ($isInvalidate) {
                    $this->cacheTags[Cache::INVALIDATE][$modelClass][$fieldName] = true;
                }
            }
        }
    }

    /**
     * Set in query part where expression 'IS NULL'
     *
     * @param  $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @version 1.3
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function isNull($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            [$fieldName => null],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NULL,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set in query part where expression '= ?' for primary key column
     *
     * @param  $pk
     * @param string|array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     *
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function pk($pk, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        if (!$isUse) {
            return $this;
        }

        if ($pk === null) {
            Logger::getInstance(__CLASS__)->exception(
                'Primary key value mast be not null',
                __FILE__,
                __LINE__,
                null,
                $this
            );
        }

        $eq = [];

        /**
         * @var Model $modelClass
         */
        $modelClass = $this->getModelClassTableAlias($modelTableData)[0];

        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

        foreach ((array)$pk as $pkName => $pkValue) {
            if (empty($pkFieldNames)) {
                break;
            }

            if (is_int($pkName)) {
                $eq[array_shift($pkFieldNames)] = $pkValue;
                continue;
            }

            if (($key = array_search($pkName, $pkFieldNames)) !== false) {
                unset($pkFieldNames[$key]);
                $eq[$pkName] = $pkValue;
            }
        }

        $this->eq($eq, $modelTableData, $sqlLogical);

        return $modelClass == $this->getModelClass()
            ? $this->limit(1)
            : $this;
    }

    /**
     * Set in query part where expression '= ?'
     *
     * @param array $fieldNameValues
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @version 1.3
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function eq(array $fieldNameValues, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $fieldNameValues,
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_OPERATOR_EQUAL,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set query part limit
     *
     * @param  $limit
     * @param int|null $offset
     * @param bool $isUse
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function limit($limit, $offset = 0, $isUse = true)
    {
        if (!$isUse) {
            return $this;
        }

        $this->sqlParts[self::PART_LIMIT] = [
            'limit' => $limit,
            'offset' => $offset
        ];

        return $this;
    }

    /**
     * Set in query part where expression '>= ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public function ge($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_OPERATOR_GREATER_OR_EQUAL,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set in query part where expression 'REGEXP ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.4
     */
    public function regex($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_KEYWORD_REGEXP,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set in query part where expression 'REGEXP ?'
     *
     * @param  $fieldName
     * @param array $fieldRangeValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   1.1
     */
    public function between($fieldName, array $fieldRangeValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            [$fieldName => $fieldRangeValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_KEYWORD_BETWEEN,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set in query part where expression '<= ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public function le($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_OPERATOR_LESS_OR_EQUAL,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set in query part where expression '> ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public function gt($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_OPERATOR_GREATER,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set in query part where expression '< ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public function lt($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_OPERATOR_LESS,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set in query part where expression '= ""'
     *
     * @param  $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return QueryBuilder
     *
     * @throws Exception
     * @version 0.6
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function isEmpty($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        return $this->eq([$fieldName => ''], $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression '<> ""'
     *
     * @param  $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return QueryBuilder
     *
     * @throws Exception
     * @version 0.6
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function notEmpty($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        return $this->ne($fieldName, '', $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression '<> ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public function ne($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_OPERATOR_NOT_EQUAL,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set in query part where expression '== 1' is boolean true(1)
     *
     * @param  $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     *
     * @throws Exception
     * @version 1.13
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     */
    public function is($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        $is = [];

        foreach ((array)$fieldName as $fn) {
            $is[$fn] = 1;
        }

        return $this->eq($is, $modelTableData, $sqlLogical, $isUse);
    }

    /**
     * Set in query part where expression '== 0' is boolean false(0)
     *
     * @param  $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     *
     * @throws Exception
     * @version 1.13
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     */
    public function not($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        $not = [];

        foreach ((array)$fieldName as $fn) {
            $not[$fn] = 0;
        }

        return $this->eq($not, $modelTableData, $sqlLogical, $isUse);
    }

    /**
     * Set in query part where expression 'like ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public function like($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_KEYWORD_LIKE,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * Set in query part where expression 'rlike ?'
     *
     * @param  $fieldName
     * @param  $value
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public function rlike($fieldName, $value, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = $this->getModelClassTableAlias($modelTableData)[0];

        $columnFieldMapping = $modelClass::getScheme()->getColumnFieldMap();
        $fieldValue = $modelClass::getFieldName($value);

        /**
         * check ability use pattern from field in base
         */
        return in_array($fieldValue, $columnFieldMapping)
            ? $this->where(
                [$fieldValue => $fieldName],
                $modelTableData,
                QueryBuilder::SQL_COMPARISON_KEYWORD_RLIKE_REVERSE,
                $sqlLogical,
                $isUse
            )
            : $this->where(
                [$fieldName => $value],
                $modelTableData,
                QueryBuilder::SQL_COMPARISON_KEYWORD_RLIKE,
                $sqlLogical,
                $isUse
            );
    }

    /**
     * Set inner join query part
     *
     * @param  $modelTableData
     * @param null $fieldNames
     * @param null $condition
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @version 1.5
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     */
    public function inner($modelTableData, $fieldNames = null, $condition = null, $isUse = true)
    {
        if ($isUse) {
            /** @var Model $modelClass */
            list($table, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

            if ($table instanceof Query) {
//                $table = $table->getQueryBuilder();
                $modelClass = $table->getQueryBuilder()->getModelClass();
            } else if ($table instanceof QueryBuilder) {
                $modelClass = $table->getModelClass();
            } else if (is_array($table)) {
                $modelClass = $this->getModelClass();
            } else {
                $modelClass = $table;
            }

            $this
                ->select($fieldNames, null, [$modelClass, $tableAlias])
                ->join(QueryBuilder::SQL_CLAUSE_INNER_JOIN, $modelTableData, $condition);
        }

        return $this;
    }

    /**
     * Set  *join query part
     *
     * @param  $joinType
     * @param Model|array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @param null $condition
     * @return QueryBuilder
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.9
     * @since   0.0
     */
    private function join($joinType, $modelTableData, $condition = null)
    {

        if (!$condition) {
            $joins = array_merge(
                [
                    $this->getTableAlias() => [
                        'type' => 'FROM',
                        'class' => $this->getModelClass()
                    ]
                ],
                $this->sqlParts[self::PART_JOIN]
            );

            $this->addJoin($joinType, $modelTableData, $joins);

            return $this;
        }

        /** @var Model $modelClass */
        list($table, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

//        if ($table instanceof Query) {
////                $table = $table->getQueryBuilder();
//            $modelClass = $table->getQueryBuilder()->getModelClass();
//        } else if ($table instanceof QueryBuilder) {
//            $modelClass = $table->getModelClass();
//        } else if (is_array($table)) {
//            $modelClass = $this->getModelClass();
//        } else {
//            $modelClass = $table;
//        }

        $this->sqlParts[self::PART_JOIN][$tableAlias] = [
            'type' => $joinType,
            'class' => $table,
            'on' => $condition
        ];

        //todo: возможно не нужен этот код
//        $this->appendCacheTag($modelClass, $fieldName, true, false);

        //todo: аналогичный код выполняется для селекта: там вытаскиываются все биндпартсы - нужно только для селектов. Аналогично здесь для джойнов
        if ($table instanceof QueryBuilder) {
            // TODO: This duplicate from Query::getBinds.. fix it
            $binds = [];

            foreach ($table->getBindParts() as $bindPart) {
                if (!is_array(reset($bindPart))) {
                    $binds = array_merge($binds, array_values($bindPart));
                    continue;
                }

                foreach ($bindPart as $values) {
                    $binds = array_merge($binds, array_values($values));
                    continue;
                }
            }

            $this->bindParts[QueryBuilder::PART_JOIN] = $binds;
        }

        return $this;
    }

    /**
     * @param $joinType
     * @param Model $modelTableData
     * @param array $joins
     * @return bool
     * @throws Exception
     * @throws QueryBuilder_Join
     * @throws \Ice\Exception\Config_Error
     */
    private function addJoin($joinType, $modelTableData, array $joins)
    {
        /** @var Model|string $modelClass */
        list($table, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        if ($table instanceof Query) {
            $modelClass = $table->getQueryBuilder()->getModelClass();
        } else if ($table instanceof QueryBuilder) {
            $modelClass = $table->getModelClass();
        } else if (is_array($table)) {
            $modelClass = $this->getModelClass();
        } else {
            $modelClass = $table;
        }

        if (isset($this->sqlParts[self::PART_JOIN][$tableAlias])) {
            Logger::getInstance(__CLASS__)->warning(
                ['Model {$0} already joined with {$1}', [$modelClass, $this->getModelClass(),]],
                __FILE__,
                __LINE__,
                null,
                $this->sqlParts
            );
        }

        foreach ($joins as $joinTableAlias => $join) {
            if (is_array($join['class'])) {
                continue;
            }

            /** @var Model $joinModelClass */
            $joinModelClass = $join['class'] instanceof QueryBuilder || $join['class'] instanceof Query
                ? $join['class']->getModelClass()
                : $join['class'];

            // todo: это лишнее, ломает кейс джойна юниона - fix it
            if (is_array($joinModelClass)) {
                continue;
            }

            $joinModelScheme = $joinModelClass::getScheme();

            $oneToMany = $joinModelScheme->gets('relations/' . ModelScheme::ONE_TO_MANY);

            if (isset($oneToMany[$modelClass])) {
                $this->sqlParts[self::PART_JOIN][$tableAlias] = [
                    'type' => $joinType,
                    'class' => $table,
                    'on' => '`' . $joinTableAlias . '`.`' . $oneToMany[$modelClass] . '` = `' .
                        $tableAlias . '`.`' . $modelClass::getPkColumnName() . '`'
                ];

                return true;
            }
        }

        foreach ($joins as $joinTableAlias => $join) {
            /** @var Model $joinModelClass */
            $joinModelClass = $join['class'] instanceof QueryBuilder || $join['class'] instanceof Query
                ? $join['class']->getModelClass()
                : $join['class'];


            // todo: это лишнее, ломает кейс джойна юниона - fix it
            if (is_array($joinModelClass)) {
                continue;
            }

            $joinModelScheme = $joinModelClass::getScheme();

            $manyToOne = $joinModelScheme->gets('relations/' . ModelScheme::MANY_TO_ONE);

            if (isset($manyToOne[$modelClass])) {
                $this->sqlParts[self::PART_JOIN][$tableAlias] = [
                    'type' => $joinType,
                    'class' => $table,
                    'on' => '`' . $tableAlias . '`.`' . $manyToOne[$modelClass] .
                        '` = `' . $joinTableAlias . '`.`' . $joinModelClass::getPkColumnName() . '`'
                ];

                return true;
            }
        }

        foreach ($joins as $joinTableAlias => $join) {
            /** @var Model $joinModelClass */
            $joinModelClass = $join['class'] instanceof QueryBuilder || $join['class'] instanceof Query
                ? $join['class']->getModelClass()
                : $join['class'];

            // todo: это лишнее, ломает кейс джойна юниона - fix it
            if (is_array($joinModelClass)) {
                continue;
            }

            $joinModelScheme = $joinModelClass::getScheme();

            $manyToMany = $joinModelScheme->gets('relations/' . ModelScheme::MANY_TO_MANY);

            if (isset($manyToMany[$modelClass])) {
                $linkClasses = $manyToMany[$modelClass];

                if (count($linkClasses) > 1) {
                    throw new QueryBuilder_Join('linkModelClass is ambiguous', $linkClasses);
                }

                $linkClass = reset($linkClasses);

                $joinAlias = Class_Object::getClassName($linkClass);

                $joinColumn = $joinModelScheme->get('relations/' . ModelScheme::MANY_TO_ONE . '/' . $linkClass);

                $this->sqlParts[self::PART_JOIN][$joinAlias] = [
                    'type' => $joinType,
                    'class' => $linkClass,
                    'on' => '`' . $joinAlias . '`.`' . $joinColumn . '` = `' .
                        $joinTableAlias . '`.`' . $joinModelClass::getPkColumnName() . '`'
                ];

                $joinColumn2 = $modelClass::getScheme()->get('relations/' . ModelScheme::MANY_TO_ONE . '/' . $linkClass);

                $this->sqlParts[self::PART_JOIN][$tableAlias] = [
                    'type' => $joinType,
                    'class' => $table,
                    'on' => '`' . $tableAlias . '`.`' . $modelClass::getPkColumnName() . '` = `' .
                        $joinAlias . '`.`' . $joinColumn2 . '`'
                ];

                return true;
            }
        }

        foreach ($joins as $joinTableAlias => $join) {
            /** @var Model $joinModelClass */
            $joinModelClass = $join['class'] instanceof QueryBuilder || $join['class'] instanceof Query
                ? $join['class']->getModelClass()
                : $join['class'];

            $joinModelScheme = $joinModelClass::getScheme();

            $joinFieldNames = $joinModelScheme->getFieldColumnMap();

            $joinModelName = Class_Object::getClassName($joinModelClass);

            $joinModelNameFk = strtolower($joinModelName . '__fk');
            $joinModelNamePk = strtolower($joinModelName) . '_pk';

            if (in_array($joinModelNameFk, $modelClass::getScheme()->getFieldNames())) {
                $this->sqlParts[self::PART_JOIN][$tableAlias] = [
                    'type' => $joinType,
                    'class' => $table,
                    'on' => $tableAlias . '.' . $modelClass::getScheme()->getFieldColumnMap()[$joinModelNameFk] . ' = ' .
                        $joinTableAlias . '.' . $joinFieldNames[$joinModelNamePk]
                ];

                return true;
            }
        }

        foreach ($joins as $joinTableAlias => $join) {
            /** @var Model $joinModelClass */
            $joinModelClass = $join['class'] instanceof QueryBuilder || $join['class'] instanceof Query
                ? $join['class']->getModelClass()
                : $join['class'];

            $joinModelScheme = $joinModelClass::getScheme();

            $joinFieldNames = $joinModelScheme->getFieldColumnMap();

            $modelName = Class_Object::getClassName($modelClass);

            $modelNameFk = strtolower($modelName . '__fk');
            $modelNamePk = strtolower($modelName) . '_pk';

            if (in_array($modelNameFk, $joinModelScheme->getFieldNames())) {
                $this->sqlParts[self::PART_JOIN][$tableAlias] = [
                    'type' => $joinType,
                    'class' => $table,
                    'on' => $tableAlias . '.' . $modelClass::getScheme()->getFieldColumnMap()[$modelNamePk] . ' = ' .
                        $joinTableAlias . '.' . $joinFieldNames[$modelNameFk]
                ];

                return true;
            }
        }

        Logger::getInstance(__CLASS__)->exception(
            ['Could not defined condition join part of query for {$0} with {$1}', [$this->getModelClass(), $modelClass]],
            __FILE__,
            __LINE__,
            null,
            $this->sqlParts
        );

        return false;
    }

    public function getBindParts()
    {
        return $this->bindParts;
    }

    /**
     * Prepare select query part
     *
     * @param  $fieldName
     * @param  $fieldAlias
     * @param array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @param bool $isUse
     * @return QueryBuilder
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.2
     */
    public function select($fieldName, $fieldAlias = null, $modelTableData = [], $isUse = true)
    {
        if (!$isUse) {
            return $this;
        }

        /** @var Model $modelClass */
        list($table, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        if ($table instanceof Query) {
            $modelClass = $table->getQueryBuilder()->getModelClass();
        } else if ($table instanceof self) {
            $modelClass = $table->getModelClass();
        } else if (is_array($table)) {
            $modelClass = $this->getModelClass();
        } else {
            $modelClass = $table;
        }

        if (!isset($this->sqlParts[self::PART_SELECT][$tableAlias])) {
            $this->sqlParts[self::PART_SELECT][$tableAlias] = [
                'table' => $table,
                'columns' => []
            ];
        }

        if ($fieldName == '/pk') {
            $fieldName = $modelClass::getScheme()->getPkFieldNames();

            if (count($fieldName) === 1) {
                $fieldName = reset($fieldName);

                if (!$fieldAlias) {
                    $fieldAlias = $modelClass::getFieldName('/pk', $tableAlias);
                }
            }
        }

        $modelScheme = $modelClass::getScheme();

        if ($fieldName == '*') {
            $fieldName = array_merge($modelScheme->getFieldNames(), $modelScheme->getPkFieldNames());
        }

        if ($fieldName !== null && $fieldName !== '') {
            if (is_array($fieldName)) {
                foreach ($fieldName as $field => $fieldAlias) {
                    if (is_numeric($field)) {
                        $this->select($fieldAlias, null, $modelTableData);
                    } else {
                        $this->select($field, $fieldAlias, $modelTableData);
                    }
                }

                return $this;
            }

            if (!$fieldAlias) {
                $fieldAlias = $modelClass::getFieldName($fieldName, $tableAlias);
            }

            $fieldName = $modelClass::getFieldName($fieldName);

            $this->sqlParts[self::PART_SELECT][$tableAlias]['columns'][$fieldAlias] = $fieldName;

            $this->appendCacheTag($modelClass, $fieldName, true, false);
        }

        if ($table instanceof QueryBuilder || $table instanceof Query) {
            $bindParts = $table instanceof Query
                ? $table->getQueryBuilder()->getBindParts()
                : $table->getBindParts();

            // TODO: This duplicate from Query::getBinds.. fix it
            $binds = [];

            foreach ($bindParts as $bindPart) {
                if (!is_array(reset($bindPart))) {
                    $binds = array_merge($binds, array_values($bindPart));
                    continue;
                }

                foreach ($bindPart as $values) {
                    $binds = array_merge($binds, array_values($values));
                    continue;
                }
            }

            $this->bindParts[QueryBuilder::PART_SELECT] = $binds;
        }

        return $this;
    }

    /**
     * Return query result for select query
     *
     *  part structure:
     * ```php
     *      $_sqlPart[self::PART_SELECT] = [
     *              $tableAlias => [
     *                  'table' => $modelClass,
     *                  'columns' => [
     *                      $fieldName => $fieldAlias,
     *                      $fieldName2 => $fieldAlias2,
     *                  ]
     *             ]
     *         ]
     *      ];
     * ```
     *
     * @param  $fieldNames
     * @param array $modelTableData
     * @param string|null $dataSourceKey
     * @return Query
     *
     * @throws Exception
     * @version 0.6
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function getSelectQuery($fieldNames, $modelTableData = [], $dataSourceKey = null)
    {
        $this->queryType = QueryBuilder::TYPE_SELECT;

        list($table, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        $this->tableAlias = $tableAlias;

        $this->select((array)$fieldNames, null, $modelTableData);

        return $this->getQuery($dataSourceKey);
    }

    /**
     * Return instance of query by current query builder
     *
     * @param string|null $dataSourceKey
     * @return Query
     *
     * @throws \Exception
     * @version 0.6
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    private function getQuery($dataSourceKey = null)
    {
        return Query::create($this, $dataSourceKey)->bind($this->bindParts);
    }

    /**
     * Set inner join query part
     *
     * @param  $modelTableData
     * @param string $fieldNames
     * @param null $condition
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @version 0.6
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function left($modelTableData, $fieldNames = null, $condition = null, $isUse = true)
    {
        if ($isUse) {
            /** @var Model $modelClass */
            list($table, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

            if ($table instanceof Query) {
//                $table = $table->getQueryBuilder();
                $modelClass = $table->getQueryBuilder()->getModelClass();
            } else if ($table instanceof QueryBuilder) {
                $modelClass = $table->getModelClass();
            } else if (is_array($table)) {
                $modelClass = $this->getModelClass();
            } else {
                $modelClass = $table;
            }

            $this
                ->select($fieldNames, null, [$modelClass, $tableAlias])
                ->join(QueryBuilder::SQL_CLAUSE_LEFT_JOIN, $modelTableData, $condition);
        }

        return $this;
    }

    /**
     * Set inner join query part
     *
     * @param $modelTableData
     * @param string $fieldNames
     * @param null $condition
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @version 1.9
     * @since   1.9
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function right($modelTableData, $fieldNames = null, $condition = null, $isUse = true)
    {
        if ($isUse) {
            /** @var Model $modelClass */
            list($table, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

            if ($table instanceof Query) {
//                $table = $table->getQueryBuilder();
                $modelClass = $table->getQueryBuilder()->getModelClass();
            } else if ($table instanceof QueryBuilder) {
                $modelClass = $table->getModelClass();
            } else if (is_array($table)) {
                $modelClass = $this->getModelClass();
            } else {
                $modelClass = $table;
            }

            $this
                ->select($fieldNames, null, [$modelClass, $tableAlias])
                ->join(QueryBuilder::SQL_CLAUSE_RIGHT_JOIN, $modelTableData, $condition);
        }

        return $this;
    }

    /**
     * Return query result for insert query
     *
     *  part structure:
     * ```php
     *      $values = [
     *          [
     *              'name' => 'Petya',
     *              'surname' => 'Ivanov'
     *          ],
     *          [
     *              'name' => 'Vasya',
     *              'surname' => 'Petrov'
     *          ],
     *      ];
     * ```
     *  example:
     * ```php
     *      ->values(['name' => 'Petya', 'surname' => 'Vasechkin'])
     *      ->values([
     *                  ['name' => 'Petya', 'surname' => 'Vasechkin'],
     *                  ['name' => 'Ivan', 'surname' => 'Petrov'],
     *      ])
     * ```
     *
     * @param array $data Key-value array
     * @param bool $update
     * @param string|null $dataSourceKey
     * @return Query
     *
     * @throws Exception
     * @version 0.6
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function getInsertQuery(array $data, $update = false, $dataSourceKey = null)
    {
        $this->queryType = QueryBuilder::TYPE_INSERT;
        $this->sqlParts[QueryBuilder::PART_VALUES]['_update'] = $update;
        return $this->affect($data, QueryBuilder::PART_VALUES, $dataSourceKey);
    }

    /**
     * Affect query
     *
     * @param array $rows Key-value array
     * @param  $part
     * @param  $dataSourceKey
     * @return Query
     *
     * @throws Exception
     * @throws \Exception
     * @since   0.1
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.13
     */
    private function affect(array $rows, $part, $dataSourceKey)
    {
        $modelClass = $this->getModelClass();

        if (empty($rows)) {
            $this->sqlParts[$part] = array_merge(
                $this->sqlParts[$part],
                [
                    'fieldNames' => [],
                    'rowCount' => 0
                ]
            );

            $this->bindParts[$part] = [[]];

            return $this->getQuery($dataSourceKey);
        }

        $firstRow = reset($rows);

        if (!is_array($firstRow)) {
            $firstRow = $rows;
            $rows = [$rows];
        }

        $fieldNames = [];

        foreach (array_keys($firstRow) as $fieldName) {
            $fieldNames[] = $modelClass::getFieldName($fieldName);
        }

        $this->sqlParts[$part] = array_merge(
            $this->sqlParts[$part],
            [
                'fieldNames' => $fieldNames,
                'rowCount' => count($rows)
            ]
        );


        $this->appendCacheTag($modelClass, $fieldNames, false, true);

        $this->bindParts[$part] = array_merge($this->bindParts[$part], $rows);

        return $this->getQuery($dataSourceKey);
    }

    /**
     * Return query result for update query
     *
     * @param array $data Key-value array
     * @param null $dataSource
     * @return Query
     *
     * @throws Exception
     * @version 0.6
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function getUpdateQuery(array $data, $dataSource = null)
    {
        //todo: на учиться делать ->eq($unique_keys => from $data), убирая эти самые ключи из $data... может еще один параметр $smart = false?
        $this->queryType = QueryBuilder::TYPE_UPDATE;
        return $this->affect($data, QueryBuilder::PART_SET, $dataSource);
    }

    /**
     * Return query result for delete query
     *
     * @param array $pkValues
     * @param string|null $dataSourceKey
     * @return Query
     *
     * @throws Exception
     * @throws \Exception
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     */
    public function getDeleteQuery($pkValues = [], $dataSourceKey = null)
    {
        $this->queryType = QueryBuilder::TYPE_DELETE;
        $this->sqlParts[QueryBuilder::PART_WHERE]['_delete'] = $this->modelClass;

        if (!empty($pkValues)) {
            $this->inPk((array)$pkValues);
        }

        return $this->getQuery($dataSourceKey);
    }

    /**
     * Build query part where primary key in
     *
     * example:
     * ```php
     *      // ...
     *      $qb->inPk([1, 3, 5']) // for User model: where `user_pk` in (1, 3, 5)
     *      // ...
     * ```
     *
     * @param array $value
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function inPk(array $value, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        if (!$isUse) {
            return $this;
        }

        /** @var Model|string $modelClass */
        $modelClass = $this->getModelClassTableAlias($modelTableData)[0];

        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

        if (count($pkFieldNames) > 1) {
            throw  new Error('not implemented');
        }

        return $this->in(reset($pkFieldNames), $value, $modelTableData, $sqlLogical);
    }

    /**
     * Set flag of get count rows
     *
     * @param array $fieldNameAlias
     * @param array $modelTableData
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     * @deprecated  1.5 use ::func
     */
    public function count($fieldNameAlias = ['/pk'], $modelTableData = [])
    {
        /**
         * @var Model $modelClass
         */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);
        list($fieldName, $fieldAlias) = $this->getFieldNameAlias($fieldNameAlias, $modelClass);
        $fieldNames = [];

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

        foreach ((array)$fieldName as $name) {
            if (isset($fieldColumnMap[$name])) {
                $name = $fieldColumnMap[$name];
                $fieldNames[] = '`' . $tableAlias . '`.`' . $modelClass::getFieldName($name) . '`';
            } else {
                $fieldNames[] = $name;
            }

            // Потенциально баг.. не понятно что приходит в name,. посмотреть аналогичные вызовы
            $this->appendCacheTag($modelClass, $name, true, false);
        }

        if (!$fieldAlias) {
            $fieldAlias = strtolower($tableAlias) . '__count';
        }

        $this->select('COUNT(' . implode(',', $fieldNames) . ')', $fieldAlias, [$modelClass, '']);

        return $this;
    }

    /**
     * Return couple fieldName and fieldAlias
     *
     * @param string|array $fieldNameAlias
     * @param Model $modelClass
     * @return array
     *
     * @throws Exception
     * @version 0.6
     * @since   0.6
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    private function getFieldNameAlias($fieldNameAlias, $modelClass)
    {
        if (empty($fieldNameAlias)) {
            $fieldName = null;
            $fieldAlias = null;
        } else {
            $fieldNameAlias = (array)$fieldNameAlias;

            if (count($fieldNameAlias) > 1) {
                $fieldNameAlias = [array_shift($fieldNameAlias) => array_shift($fieldNameAlias)];
            }

            // deprecated 7.2
            //list($fieldName, $fieldAlias) = each($fieldNameAlias);

            $fieldAlias = reset($fieldNameAlias);
            $fieldName = key($fieldNameAlias);

            if (is_int($fieldName)) {
                $fieldName = $fieldAlias;
                $fieldAlias = null;
            }
        }

        if ($fieldName == '/pk') {
            $fieldName = $modelClass::getScheme()->getPkFieldNames();
        }

        return [$fieldName, $fieldAlias];
    }

    /**
     * Set flag of get count rows
     *
     * @param array $fieldNameAlias
     * @param array $modelTableData
     * @return QueryBuilder
     *
     * @throws Exception
     * @deprecated 1.5 use ::func
     * @version 0.6
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function groupConcat($fieldNameAlias = [], $modelTableData = [])
    {
        /**
         * @var Model $modelClass
         */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);
        list($fieldName, $fieldAlias) = $this->getFieldNameAlias($fieldNameAlias, $modelClass);
        $fieldNames = [];

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

        foreach ((array)$fieldName as $name) {
            if (isset($fieldColumnMap[$name])) {
                $name = $fieldColumnMap[$name];
                $fieldNames[] = '`' . $tableAlias . '`.`' . $modelClass::getFieldName($name) . '`';
            } else {
                $fieldNames[] = $name;
            }

            // Потенциально баг.. не понятно что приходит в name,. посмотреть аналогичные вызовы
            $this->appendCacheTag($modelClass, $name, true, false);
        }

        if (!$fieldAlias) {
            $fieldAlias = strtolower($tableAlias) . '__count';
        }

        $this->select('GROUP_CONCAT(' . implode(',', $fieldNames) . ')', $fieldAlias, [$modelClass, '']);

        return $this;
    }

    /**
     * Ascending ordering
     *
     * @param string $fieldName
     * @param array|string $modelTableData
     * @param bool $isUse
     * @return QueryBuilder
     *
     * @throws Exception
     * @version 0.6
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     */
    public function asc($fieldName = '/pk', $modelTableData = [], $isUse = true)
    {
        if (!$isUse) {
            return $this;
        }

        return $this->order($fieldName, QueryBuilder::SQL_ORDERING_ASC, $modelTableData);
    }

    /**
     * Ordering
     *
     * @param  $fieldName
     * @param  $ascOrDesc
     * @param array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @return QueryBuilder
     *
     * @throws Exception
     * @version 1.9
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    private function order($fieldName, $ascOrDesc, $modelTableData = [])
    {
        if (is_array($fieldName)) {
            foreach ($fieldName as $name) {
                $this->order($name, $ascOrDesc, $modelTableData);
            }

            return $this;
        }

        /**
         * @var Model $modelClass
         */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        $this->sqlParts[self::PART_ORDER][] = [
            'modelClass' => $modelClass,
            'tableAlias' => $tableAlias,
            'fieldName' => $fieldName ? $modelClass::getFieldName($fieldName) : null,
            'order' => $ascOrDesc
        ];

        return $this;
    }

    /**
     * Order by RAND()
     *
     * @return QueryBuilder
     * @throws Exception
     */
    public function rand()
    {
        return $this->order(null, QueryBuilder::SQL_ORDERING_RAND);
    }

    /**
     * grouping by
     *
     * @param  $fieldName
     * @param array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @param bool $isUse
     * @return QueryBuilder
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function group($fieldName = null, $modelTableData = [], $isUse = true)
    {
        if (!$isUse) {
            return $this;
        }

        if (is_array($fieldName)) {
            foreach ($fieldName as $name) {
                $this->group($name, $modelTableData);
            }

            return $this;
        }

        /**
         * @var Model $modelClass
         */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        if (!$fieldName || $fieldName == '/pk') {
            $this->group($modelClass::getScheme()->getPkFieldNames(), $modelTableData);

            return $this;
        }

        $fieldName = $modelClass::getFieldName($fieldName);

        if (!isset($this->sqlParts[self::PART_GROUP][$tableAlias])) {
            $this->sqlParts[self::PART_GROUP][$tableAlias] = [
                $modelClass, [$fieldName]
            ];
        } else {
            $this->sqlParts[self::PART_GROUP][$tableAlias][1][] = $fieldName;
        }

        return $this;
    }

    /**
     * Descending ordering
     *
     * @param  $fieldName
     * @param array $modelTableData
     * @return QueryBuilder
     *
     * @throws Exception
     * @version 0.6
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function desc($fieldName = '/pk', $modelTableData = [], $isUse = true)
    {
        if (!$isUse) {
            return $this;
        }

        return $this->order($fieldName, QueryBuilder::SQL_ORDERING_DESC, $modelTableData);
    }

    /**
     * Execute query create table
     *
     * @param string|null $dataSourceKey
     * @return Query
     *
     * @throws Exception
     * @throws \Ice\Exception\Config_Error
     * @throws \Ice\Exception\Config_Error
     * @version 0.6
     * @since   0.2
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function createTableQuery($dataSourceKey = null)
    {
        $modelClass = $this->modelClass;

        foreach ($modelClass::getScheme()->gets('columns') as $columnName => $column) {
            $this->column($columnName, $column['scheme']);
        }

        $this->queryType = QueryBuilder::TYPE_CREATE;
        return $this->getQuery($dataSourceKey);
    }

    /**
     * Set column part for create or alter table
     *
     * @param  $name
     * @param array $scheme
     * @param null $modelClass
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     */
    public function column($name, array $scheme, $modelClass = null)
    {
        if (!$modelClass) {
            $modelClass = $this->getModelClass();
        }

        if (isset($this->sqlParts[QueryBuilder::PART_CREATE][$modelClass])) {
            $this->sqlParts[QueryBuilder::PART_CREATE][$modelClass][$name] = $scheme;
        } else {
            $this->sqlParts[QueryBuilder::PART_CREATE][$modelClass] = [$name => $scheme];
        }
        return $this;
    }

    /**
     * Execute query drop table
     *
     * @param string|null $dataSourceKey
     * @return Query
     *
     * @throws \Exception
     * @version 1.13
     * @since   0.2
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function dropTableQuery($dataSourceKey = null)
    {
        $this->queryType = QueryBuilder::TYPE_DROP;

        $this->sqlParts[QueryBuilder::PART_DROP] = [$this->getModelClass() => $this->getTableAlias()];

        return $this->getQuery($dataSourceKey);
    }

    /**
     * Clone current query builder
     *
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     */
    public function cloneBuilder()
    {
        return clone $this;
    }

    /**
     * Set in query part where expression for search
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public function search($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SEARCH_KEYWORD,
            $sqlLogical,
            $isUse
        );
    }

    public function afterSelect($trigger, $params = [], $modelClass = null, $isUse = true)
    {
        return $this->addTrigger('afterSelect', $trigger, $params, $modelClass, $isUse);
    }

    private function addTrigger($type, $trigger, $params, $modelClass, $isUse)
    {
        if (!$isUse) {
            return $this;
        }

        $modelClass = $modelClass
            ? Model::getClass($modelClass)
            : $this->getModelClass();

        $trigger = is_callable($trigger) ? $trigger : $trigger . 'Trigger';

        $this->triggers[$type][] = [$trigger, $params, $modelClass];

        return $this;
    }

    public function afterSelectCallback($callback, $params = [], $modelClass = null, $isUse = true)
    {
        return $this->addTrigger('afterSelect', $callback, $params, $modelClass, $isUse);
    }

    public function beforeSelect($trigger, $params = [], $modelClass = null, $isUse = true)
    {
        return $this->addTrigger('beforeSelect', $trigger, $params, $modelClass, $isUse);
    }

    public function afterInsert($trigger, $params = [], $modelClass = null, $isUse = true)
    {
        return $this->addTrigger('afterInsert', $trigger, $params, $modelClass, $isUse);
    }

    public function beforeInsert($trigger, $params = [], $modelClass = null, $isUse = true)
    {
        return $this->addTrigger('beforeInsert', $trigger, $params, $modelClass, $isUse);
    }

    public function afterUpdate($trigger, $params = [], $modelClass = null, $isUse = true)
    {
        return $this->addTrigger('afterUpdate', $trigger, $params, $modelClass, $isUse);
    }

    public function beforeUpdate($trigger, $params = [], $modelClass = null, $isUse = true)
    {
        return $this->addTrigger('beforeUpdate', $trigger, $params, $modelClass, $isUse);
    }

    public function afterDelete($trigger, $params = [], $modelClass = null, $isUse = true)
    {
        return $this->addTrigger('afterDelete', $trigger, $params, $modelClass, $isUse);
    }

    public function beforeDelete($trigger, $params = [], $modelClass = null, $isUse = true)
    {
        return $this->addTrigger('beforeDelete', $trigger, $params, $modelClass, $isUse);
    }

    /**
     * @param Widget[]|mixed $widgets
     * @param bool $applyWidgetQueryBuilderParts
     * @return QueryBuilder
     */
    public function attachWidgets($widgets, $applyWidgetQueryBuilderParts = true)
    {
        if (is_object($widgets)) {
            $widgets = [$widgets];
        }

        foreach ($widgets as $widget) {
            if ($applyWidgetQueryBuilderParts) {
                $widget->queryBuilderPart($this);
            }

            $this->widgets[] = $widget;
        }

        return $this;
    }

    public function orderWidget($widgetName, $key, $value, $fieldName = null, $modelTableData = [])
    {
        $widget = $this->widgets[$widgetName]->set([$key => $value]);
        $value = $widget->get($key);

        if (!empty($value)) {
            if (!$fieldName) {
                $fieldName = $key;
            }

            $this->order($fieldName, $value, $modelTableData);
        }

        return $this;
    }

    /**
     * Set Limits and offset by page and limit
     *
     * @param null $page
     * @param null $limit
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function setPagination($page = null, $limit = null)
    {
        $page = $page
            ? (int)$page
            : self::DEFAULT_PAGINATION_PAGE;

        $limit = $limit === null
            ? self::DEFAULT_PAGINATION_LIMIT
            : (int)$limit;

        return $this
            ->setCalcFoundRows()
            ->limit($limit, ($page - 1) * $limit);
    }

    /**
     * Set flag for total found rows query
     *
     * @param bool $calcFoundRows
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function setCalcFoundRows($calcFoundRows = true)
    {
        $this->sqlParts[self::PART_SELECT]['_calcFoundRows'] = $calcFoundRows;
        return $this;
    }

    public function isCalcFoundRows()
    {
        return $this->sqlParts[self::PART_SELECT]['_calcFoundRows'];
    }

    public function setDistinct($distinct = true)
    {
        $this->sqlParts[self::PART_SELECT]['_distinct'] = $distinct;
        return $this;
    }

    public function isDistinct()
    {
        return $this->sqlParts[self::PART_SELECT]['_distinct'];
    }

    public function setSqlNoCache($sqlNoCache = true)
    {
        $this->sqlParts[self::PART_SELECT]['_sqlNoCache'] = $sqlNoCache;
        return $this;
    }

    public function isSqlNoCache()
    {
        return $this->sqlParts[self::PART_SELECT]['_sqlNoCache'];
    }

    public function filter(Form $form)
    {
        foreach ($form->getParts() as $widgetComponent) {
            $widgetComponent->filter($this);
        }

        return $this;
    }

    public function addTransform($transform, array $data = [], $modelClass = null, $isUse = true)
    {
        if (!$isUse) {
            return $this;
        }

        $modelClass = $modelClass
            ? Model::getClass($modelClass)
            : $this->getModelClass();

        $this->transforms[] = [$transform . 'Transform', $data, $modelClass];

        return $this;
    }

    /**
     * @return string
     */
    public function getQueryType()
    {
        return $this->queryType;
    }

    /**
     * @param string|null $queryPart
     * @return array
     */
    public function getSqlParts($queryPart = null)
    {
        if (!$queryPart) {
            return $this->sqlParts;
        }

        return isset($this->sqlParts[$queryPart]) ? $this->sqlParts[$queryPart] : null;
    }

    /**
     * @return array
     */
    public function getTriggers()
    {
        return $this->triggers;
    }

    /**
     * @return array
     */
    public function getCacheTags()
    {
        return $this->cacheTags;
    }

    /**
     * @return array
     */
    public function getTransforms()
    {
        return $this->transforms;
    }

    /**
     * @return Widget[]
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

    /**
     * @param $funcName
     * @param $argumentString
     * @param array $modelTableData
     * @param bool $isUse
     * @return $this
     * @throws Exception
     * @todo feature using: ->func('fieldAlias', 'funcName', 'funcArgument', $modelTableData)
     */
    public function func($funcName, $argumentString, $modelTableData = [], $isUse = true)
    {
        if (!$isUse) {
            return $this;
        }

        /**
         * @var Model $modelClass
         */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);
        list($fieldName, $fieldAlias) = $this->getFieldNameAlias($funcName, $modelClass);


// TODO: Это доработать когда смогу брать кеш теги из сабквери билдер
// Проблема: ->groupConcat(['CONCAT(" ",author_surname," ",author_name,".",author_patronymic,".")' => 'authors'])
//        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();
//            if (isset($fieldColumnMap[$fieldName])) {
//                $name = $fieldColumnMap[$fieldName];
//                $this->appendCacheTag($modelClass, $name, true, false);
//                $fieldName = '`' . $tableAlias . '`.`' . $modelClass::getFieldName($name) . '`';
//            }

        if (!$fieldAlias) {
            $fieldAlias = strtolower($tableAlias) . '__' . strtolower($funcName);
        }

        $modelScheme = $modelClass::getScheme();

        $fieldColumns = $modelScheme->getFieldColumnMap();

        $this->select(
            ($fieldName ? strtoupper($fieldName) : '') . '(' .
            (isset($fieldColumns[$argumentString])
                ? $tableAlias . '.' . $fieldColumns[$argumentString]
                : ($argumentString === '' ? '""' : $argumentString)
            ) .
            ')',
            $fieldAlias,
            $modelTableData
        );

        return $this;
    }

    /**
     * @param $part
     * @return QueryBuilder
     * @throws Error
     * @throws FileNotFound
     */
    public function resetPart($part)
    {
        switch ($part) {
            case QueryBuilder::PART_CREATE:
                $this->sqlParts[QueryBuilder::PART_CREATE] = QueryBuilder::DEFAULT_PART_CREATE;
                break;
            case QueryBuilder::PART_DROP:
                $this->sqlParts[QueryBuilder::PART_DROP] = QueryBuilder::DEFAULT_PART_DROP;
                break;
            case QueryBuilder::PART_GROUP:
                $this->sqlParts[QueryBuilder::PART_GROUP] = QueryBuilder::DEFAULT_PART_GROUP;
                break;
            case QueryBuilder::PART_HAVING:
                $this->sqlParts[QueryBuilder::PART_HAVING] = QueryBuilder::DEFAULT_PART_HAVING;
                break;
            case QueryBuilder::PART_JOIN:
                $this->sqlParts[QueryBuilder::PART_JOIN] = QueryBuilder::DEFAULT_PART_JOIN;
                break;
            case QueryBuilder::PART_LIMIT:
                $this->sqlParts[QueryBuilder::PART_LIMIT] = QueryBuilder::DEFAULT_PART_ORDER;
                break;
            case QueryBuilder::PART_ORDER:
                $this->sqlParts[QueryBuilder::PART_ORDER] = QueryBuilder::DEFAULT_PART_ORDER;
                break;
            case QueryBuilder::PART_SELECT:
                $this->sqlParts[QueryBuilder::PART_SELECT] = QueryBuilder::DEFAULT_PART_SELECT;
                break;
            case QueryBuilder::PART_SET:
                $this->sqlParts[QueryBuilder::PART_SET] = QueryBuilder::DEFAULT_PART_SET;
                break;
            case QueryBuilder::PART_VALUES:
                $this->sqlParts[QueryBuilder::PART_VALUES] = QueryBuilder::DEFAULT_PART_VALUES;
                break;
            case QueryBuilder::PART_WHERE:
                $this->sqlParts[QueryBuilder::PART_WHERE] = QueryBuilder::DEFAULT_PART_WHERE;
                break;
            default:
                throw new Error(['Unknown query builder part {$0}', $part]);
        }

        return $this;
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return QueryBuilder
     * @throws Exception
     */
    public function havingLike($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        return $this->having(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_KEYWORD_LIKE,
            $sqlLogical
        );
    }

    /**
     * @param $fieldNameValues
     * @param array $modelTableData
     * @param string $sqlComparison
     * @param string $sqlLogical
     * @param bool $isUse
     * @param string $part
     * @return QueryBuilder
     * @throws Exception
     */
    public function having($fieldNameValues, $modelTableData = [], $sqlComparison = QueryBuilder::SQL_COMPARISON_OPERATOR_RAW, $sqlLogical = QueryBuilder::SQL_LOGICAL_OR, $isUse = true, $part = QueryBuilder::PART_HAVING)
    {
        return $this->where($fieldNameValues, $modelTableData, $sqlComparison, $sqlLogical, $isUse, $part);
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return QueryBuilder
     * @throws Exception
     */
    public function havingGt($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        return $this->having(
            [$fieldName => $fieldValue],
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_OPERATOR_GREATER,
            $sqlLogical
        );
    }

    /**
     * @param $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     */
    public function havingIs($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        $is = [];

        foreach ((array)$fieldName as $fn) {
            $is[$fn] = 1;
        }

        return $this->havingEq($is, $modelTableData, $sqlLogical, $isUse);
    }

    /**
     * @param array $fieldNameValues
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     */
    public function havingEq(array $fieldNameValues, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->having(
            $fieldNameValues,
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_OPERATOR_EQUAL,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * @param $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     */
    public function havingNot($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        $not = [];

        foreach ((array)$fieldName as $fn) {
            $not[$fn] = 0;
        }

        return $this->havingEq($not, $modelTableData, $sqlLogical, $isUse);
    }

    /**
     * @param $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @throws Exception
     */
    public function havingNotNull($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        $eq = [];

        foreach ((array)$fieldName as $fn) {
            $eq[$fn] = null;
        }

        return $this->having(
            $eq,
            $modelTableData,
            QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL,
            $sqlLogical,
            $isUse
        );
    }

    /**
     * @param $scope
     * @param array $data
     * @param Model $modelClass
     * @return $this
     * @throws Exception
     */
    public function scope($scope, array $data = [], $modelClass = null)
    {
        $modelClass = $modelClass
            ? Model::getClass($modelClass)
            : $this->getModelClass();

        Query_Scope::getInstance(str_replace('Model', 'Query\Scope', $modelClass))->$scope($this, $data, $modelClass);

        return $this;
    }

    /**
     * @param $model
     * @param $fieldNames
     * @return $this
     * @todo
     */
    public function with($model, $fieldNames)
    {
        return $this;
    }

    public function inc($funcName, $step, $modelTableData = [])
    {
    }

    public function dec($funcName, $step, $modelTableData = [])
    {
    }

    public function callback($filterFunction)
    {
        call_user_func($filterFunction, $this);
    }
}
