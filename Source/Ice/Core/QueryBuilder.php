<?php
/**
 * Ice core query builder class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Exception\Error;
use Ice\Exception\QueryBuilder_Join;
use Ice\Helper\Object;
use Ice\QueryTranslator\Defined;
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
    const SQL_CLAUSE_INNER_JOIN = 'INNER JOIN';
    const SQL_CLAUSE_LEFT_JOIN = 'LEFT JOIN';
    const SQL_CLAUSE_KEYWORD_JOIN = 'JOIN';
    const SQL_LOGICAL_AND = 'AND';
    const SQL_LOGICAL_OR = 'OR';
    const SQL_LOGICAL_NOT = 'NOT';
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
    const SEARCH_KEYWORD = '$search';

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
        self::PART_CREATE => [],
        self::PART_DROP => ['_drop' => null],
        self::PART_SELECT => ['_calcFoundRows' => null],
        self::PART_VALUES => ['_update' => null],
        self::PART_SET => [],
        self::PART_JOIN => [],
        self::PART_WHERE => ['_delete' => null],
        self::PART_GROUP => [],
        self::PART_HAVING => [],
        self::PART_ORDER => [],
        self::PART_LIMIT => ['offset' => 0, 'limit' => 0]
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
     * @param  string $modelClass Class of model
     * @param  string|null $tableAlias Alias of table in query (default: class name of model)
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public static function create($modelClass, $tableAlias = null)
    {
        $queryBuilder = new QueryBuilder();

        $queryBuilder->modelClass = Model::getClass($modelClass);

        if (!$tableAlias) {
            $tableAlias = $modelClass;
        }

        $queryBuilder->tableAlias = Object::getClassName($tableAlias);

        return $queryBuilder;
    }

    /**
     * Set in query part where expression 'IS NOT NULL'
     *
     * @param  $fieldName
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function notNull($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => null],
            QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL,
            $modelTableData,
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
     * @param  $sqlLogical
     * @param  array $fieldNameValues
     * @param  $sqlComparison
     * @param  array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @param $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    private function where($sqlLogical, array $fieldNameValues, $sqlComparison, $modelTableData, $isUse)
    {
        if (!$isUse) {
            return $this;
        }

        /**
         * @var Model $modelClass
         */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        foreach ($fieldNameValues as $fieldName => $value) {
            $fieldName = $modelClass::getFieldName($fieldName);

            if ($value instanceof Model) {
                $value = $value->getPk();
                $fieldName .= '__fk';
            }

            $where = [$sqlLogical, $fieldName, $sqlComparison, $value === null ? 1 : count((array)$value)];

//            if (isset($this->sqlParts[QueryBuilder::PART_WHERE][$tableAlias])) {
//                $this->sqlParts[QueryBuilder::PART_WHERE][$tableAlias]['data'][] = $where;
//            } else {
                $this->sqlParts[QueryBuilder::PART_WHERE][] = [
                    'class' => $modelClass,
                    'alias' => $tableAlias,
                    'data' => [$where]
                ];
//            }

            $this->appendCacheTag($modelClass, $fieldName, true, false);

            if ($sqlComparison != QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NULL && $sqlComparison != QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL) {
                $this->bindParts[QueryBuilder::PART_WHERE][] = $value === null
                    ? [null]
                    : (array)$value;
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
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
     */
    public function getModelClassTableAlias($modelTableData)
    {
        if (is_object($modelTableData)) {
            $modelTableData = [$modelTableData];
        }

        if (empty($modelTableData)) {
            $modelClass = null;
            $tableAlias = null;
        } else {
            $modelTableData = (array)$modelTableData;
            if (count($modelTableData) > 1) {
                $modelTableData = [array_shift($modelTableData) => array_shift($modelTableData)];
            }

            list($modelClass, $tableAlias) = each($modelTableData);

            if (is_int($modelClass)) {
                $modelClass = $tableAlias;
                $tableAlias = null;
            }
        }

        $modelClass = !$modelClass
            ? $this->getModelClass()
            : (is_object($modelClass) ? $modelClass : Model::getClass($modelClass));

        if ($tableAlias === null) {
            $tableAlias = (is_object($modelClass) ? $modelClass->getModelClass() : $modelClass);
        }

        if ($tableAlias !== '') {
            $tableAlias = Object::getClassName($tableAlias);
        }

        return [$modelClass, $tableAlias];
    }

    /**
     * Return model class for query
     *
     * @return Model
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
     * Append cache validate or invalidate tags for this query builder
     *
     * @param Model $modelClass
     * @param $fieldNames
     * @param $isValidate
     * @param $isInvalidate boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
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
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function isNull($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => null],
            QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NULL,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set in query part where expression '= ?' for primary key column
     *
     * @param  $pk
     * @param  $modelTableData
     * @param  string $sqlLogical
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function pk($pk, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        if (empty($pk)) {
            Logger::getInstance(__CLASS__)->exception(
                'Primary key value mast be not empty',
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

        return $this->eq($eq, $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression '= ?'
     *
     * @param  array $fieldNameValues
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function eq(array $fieldNameValues, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            $fieldNameValues,
            QueryBuilder::SQL_COMPARISON_OPERATOR_EQUAL,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set in query part where expression '>= ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function ge($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SQL_COMPARISON_OPERATOR_GREATER_OR_EQUAL,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set in query part where expression 'REGEXP ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.4
     */
    public function regex($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SQL_COMPARISON_KEYWORD_REGEXP,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set in query part where expression 'REGEXP ?'
     *
     * @param  $fieldName
     * @param  $fieldRangeValue
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    public function between($fieldName, array $fieldRangeValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldRangeValue],
            QueryBuilder::SQL_COMPARISON_KEYWORD_BETWEEN,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set in query part where expression '<= ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function le($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SQL_COMPARISON_OPERATOR_LESS_OR_EQUAL,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set in query part where expression '> ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function gt($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SQL_COMPARISON_OPERATOR_GREATER,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set in query part where expression '< ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function lt($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SQL_COMPARISON_OPERATOR_LESS,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set in query part where expression '= ""'
     *
     * @param  $fieldName
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function isEmpty($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        return $this->eq([$fieldName => ''], $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression '<> ""'
     *
     * @param  $fieldName
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function notEmpty($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        return $this->ne([$fieldName => ''], $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression '<> ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function ne($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SQL_COMPARISON_OPERATOR_NOT_EQUAL,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set in query part where expression 'not in (?)'
     *
     * @param  $fieldName
     * @param  array $fieldValue
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function notIn($fieldName, array $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SQL_COMPARISON_KEYWORD_NOT_IN,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set in query part where expression '== 1' is boolean true(1)
     *
     * @param  $fieldName
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function is($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        return $this->eq([$fieldName => 1], $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression '== 0' is boolean false(0)
     *
     * @param  $fieldName
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function not($fieldName, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        return $this->eq([$fieldName => 0], $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression 'like ?'
     *
     * @param  $fieldName
     * @param  $fieldValue
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function like($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SQL_COMPARISON_KEYWORD_LIKE,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set in query part where expression 'rlike ?'
     *
     * @param  $fieldName
     * @param  $value
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
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
                $sqlLogical,
                [$fieldValue => $fieldName],
                QueryBuilder::SQL_COMPARISON_KEYWORD_RLIKE_REVERSE,
                $modelTableData,
                $isUse
            )
            : $this->where(
                $sqlLogical,
                [$fieldName => $value],
                QueryBuilder::SQL_COMPARISON_KEYWORD_RLIKE,
                $modelTableData,
                $isUse
            );
    }

    /**
     * Set inner join query part
     *
     * @param  $modelTableData
     * @param  string $fieldNames
     * @param  null $condition
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function inner($modelTableData, $fieldNames = null, $condition = null)
    {
        return $this->select($fieldNames, null, $modelTableData)
            ->join(QueryBuilder::SQL_CLAUSE_INNER_JOIN, $modelTableData, $condition);
    }

    /**
     * Set  *join query part
     *
     * @param  $joinType
     * @param  array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @param  null $condition
     * @return QueryBuilder
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    private function join($joinType, $modelTableData, $condition = null)
    {
        /**
         * @var Model $modelClass
         */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

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

            if ($this->addJoin($joinType, $modelClass, $tableAlias, $joins)) {
                return $this;
            }
        }

        if (!$condition) {
            Logger::getInstance(__CLASS__)->exception(
                ['Could not defined condition join part of query for {$0} with {$1}', [$this->getModelClass(), $modelClass]],
                __FILE__,
                __LINE__,
                null,
                $this->sqlParts
            );
        }

        $this->sqlParts[self::PART_JOIN][$tableAlias] = [
            'type' => $joinType,
            'class' => $modelClass,
            'on' => $condition
        ];

        return $this;
    }

    /**
     * Return table alias for model class for query
     *
     * @return Model
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
     * @param $joinType
     * @param Model $modelClass
     * @param $tableAlias
     * @param array $joins
     * @return bool
     * @throws Error
     * @throws QueryBuilder_Join
     */
    private function addJoin($joinType, $modelClass, $tableAlias, array $joins)
    {
        if (isset($this->sqlParts[self::PART_JOIN][$tableAlias])) {
            return false; // todo: may be exception?
        }

        foreach ($joins as $joinTableAlias => $join) {
            if (!isset($join['class'])) {
                throw new Error('Unknown how join table in query', ['builder' => $this, 'join' => $join]);
            }

            $joinModelScheme = $join['class']::getScheme();

            $oneToMany = $joinModelScheme->gets('relations/' . ModelScheme::ONE_TO_MANY);

            if (isset($oneToMany[$modelClass])) {
                $this->sqlParts[self::PART_JOIN][$tableAlias] = [
                    'type' => $joinType,
                    'class' => $modelClass,
                    'on' => '`' . $joinTableAlias . '`.`' . $oneToMany[$modelClass] . '` = `' .
                        $tableAlias . '`.`' . $modelClass::getPkColumnName() . '`'
                ];

                return true;
            }
        }

        foreach ($joins as $joinTableAlias => $join) {
            $joinModelScheme = $join['class']::getScheme();

            $manyToOne = $joinModelScheme->gets('relations/' . ModelScheme::MANY_TO_ONE);

            if (isset($manyToOne[$modelClass])) {
                $this->sqlParts[self::PART_JOIN][$tableAlias] = [
                    'type' => $joinType,
                    'class' => $modelClass,
                    'on' => '`' . $tableAlias . '`.`' . $manyToOne[$modelClass] . '` = `' .
                        $joinTableAlias . '`.`' . $join['class']::getPkColumnName() . '`'
                ];

                return true;
            }
        }

        foreach ($joins as $joinTableAlias => $join) {
            $joinModelScheme = $join['class']::getScheme();

            $manyToMany = $joinModelScheme->gets('relations/' . ModelScheme::MANY_TO_MANY);

            if (isset($manyToMany[$modelClass])) {

                $linkClasses = $manyToMany[$modelClass];

                if (count($linkClasses) > 1) {
                    throw new QueryBuilder_Join('linkModelClass is ambiguous', $linkClasses);
                }

                $linkClass = reset($linkClasses);

                $joinAlias = Object::getClassName($linkClass);

                $joinColumn = $joinModelScheme->get('relations/' . ModelScheme::MANY_TO_ONE . '/' . $linkClass);

                $this->sqlParts[self::PART_JOIN][$joinAlias] = [
                    'type' => $joinType,
                    'class' => $linkClass,
                    'on' => '`' . $joinAlias . '`.`' . $joinColumn . '` = `' .
                        $joinTableAlias . '`.`' . $join['class']::getPkColumnName() . '`'
                ];

                $joinColumn2 = $modelClass::getScheme()->get('relations/' . ModelScheme::MANY_TO_ONE . '/' . $linkClass);

                $this->sqlParts[self::PART_JOIN][$tableAlias] = [
                    'type' => $joinType,
                    'class' => $modelClass,
                    'on' => '`' . $tableAlias . '`.`' . $modelClass::getPkColumnName() . '` = `' .
                        $joinAlias . '`.`' . $joinColumn2 . '`'
                ];

                return true;
            }
        }

        foreach ($joins as $joinTableAlias => $join) {
            $joinModelScheme = $join['class']::getScheme();

            $joinFieldNames = $joinModelScheme->getFieldColumnMap();

            $joinModelName = Object::getClassName($join['class']);

            $joinModelNameFk = strtolower($joinModelName . '__fk');
            $joinModelNamePk = strtolower($joinModelName) . '_pk';

            if (in_array($joinModelNameFk, $modelClass::getScheme()->getFieldNames())) {
                $this->sqlParts[self::PART_JOIN][$tableAlias] = [
                    'type' => $joinType,
                    'class' => $modelClass,
                    'on' => $tableAlias . '.' . $modelClass::getScheme()->getFieldColumnMap()[$joinModelNameFk] . ' = ' .
                        $joinTableAlias . '.' . $joinFieldNames[$joinModelNamePk]
                ];

                return true;
            }
        }

        foreach ($joins as $joinTableAlias => $join) {
            $joinModelScheme = $join['class']::getScheme();

            $joinFieldNames = $joinModelScheme->getFieldColumnMap();

            $modelName = Object::getClassName($modelClass);

            $modelNameFk = strtolower($modelName . '__fk');
            $modelNamePk = strtolower($modelName) . '_pk';

            if (in_array($modelNameFk, $joinModelScheme->getFieldNames())) {
                $this->sqlParts[self::PART_JOIN][$tableAlias] = [
                    'type' => $joinType,
                    'class' => $modelClass,
                    'on' => $tableAlias . '.' . $modelClass::getScheme()->getFieldColumnMap()[$modelNamePk] . ' = ' .
                        $joinTableAlias . '.' . $joinFieldNames[$modelNameFk]
                ];

                return true;
            }
        }

        return false;
    }

    /**
     * Prepare select query part
     *
     * @param  $fieldName
     * @param  $fieldAlias
     * @param  array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.2
     */
    private function select($fieldName, $fieldAlias, $modelTableData)
    {
        /**
         * @var Model $modelClass
         */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        if ($modelClass instanceof Query) {
            $modelClass = $modelClass->getQueryBuilder();
        }

        if ($modelClass instanceof QueryBuilder) {
            $table = $modelClass;
            $modelClass = $modelClass->getModelClass();
        } else {
            $table = $modelClass;
        }

        if ($tableAlias && !isset($this->sqlParts[self::PART_SELECT][$modelClass][$tableAlias])) {
//            $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();
            $this->sqlParts[self::PART_SELECT][$modelClass][$tableAlias] = [
                'table' => $table,
                'columns' => [] //$table instanceof QueryBuilder ? [] : array_combine($pkFieldNames, $pkFieldNames)
            ];
        }

        if ($fieldName === null) {
            return $this; // todo: убрать когда отрефакторятся не обязательные поля (/pk ...)
        }

        if ($fieldName == '/pk') {
            $fieldName = $modelClass::getScheme()->getPkFieldNames();

            if (count($fieldName) === 1) {
                $fieldName = reset($fieldName);
            }
        }

        if ($fieldName == '*') {
            $fieldName = array_merge(
                $modelClass::getScheme()->getFieldNames(), 
                $modelClass::getScheme()->getPkFieldNames()
            );
        }
//
        if (is_array($fieldName)) {
            foreach ($fieldName as $field => $fieldAlias) {
                if (is_numeric($field)) {
                    $this->select($fieldAlias, null, $modelTableData);
                } else {
                    $this->select($field, $fieldAlias, $modelTableData);
                }
            }

            return $this;
        } else {
            $fieldName = explode(', ', $fieldName);

            if (count($fieldName) > 1) {
                $this->select($fieldName, null, $modelTableData);

                return $this;
            } else {
                $fieldName = reset($fieldName);
            }
        }

        $fieldName = $modelClass::getFieldName($fieldName);

        if (!$fieldAlias) {
            $fieldAlias = $fieldName;
        }

        $this->sqlParts[self::PART_SELECT][$modelClass][$tableAlias]['columns'][$fieldAlias] = $fieldName;

        $this->appendCacheTag($modelClass, $fieldName, true, false);

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
     *          $modelClass => [
     *              $tableAlias, [
     *                  $fieldName => $fieldAlias,
     *               $fieldName2 => $fieldAlias2,
     *             ]
     *         ]
     *      ];
     * ```
     *
     * @param  $fieldNames
     * @param  array $modelTableData
     * @param  string|null $dataSourceKey
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getSelectQuery($fieldNames, $modelTableData = [], $dataSourceKey = null)
    {
        $this->queryType = QueryBuilder::TYPE_SELECT;

        $this->select((array)$fieldNames, null, $modelTableData);

        return $this->getQuery($dataSourceKey);
    }

    /**
     * Return instance of query by current query builder
     *
     * @param  string|null $dataSourceKey
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    private function getQuery($dataSourceKey = null)
    {
        return Query::create($this, $dataSourceKey)->bind($this->bindParts);
    }

    /**
     * Set inner join query part
     *
     * @param  $modelTableData
     * @param  string $fieldNames
     * @param  null $condition
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function left($modelTableData, $fieldNames = '/pk', $condition = null)
    {
        return $this->select($fieldNames, null, $modelTableData)
            ->join(QueryBuilder::SQL_CLAUSE_LEFT_JOIN, $modelTableData, $condition);
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
     * @param  array $data Key-value array
     * @param  bool $update
     * @param  string|null $dataSourceKey
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
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
     * @param  array $data Key-value array
     * @param  $part
     * @param  $dataSourceKey
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.1
     */
    private function affect(array $data, $part, $dataSourceKey)
    {
        $modelClass = $this->getModelClass();

        if (empty($data)) {
            $this->sqlParts[$part] = array_merge(
                $this->sqlParts[$part],
                [
                    'modelClass' => $modelClass,
                    'fieldNames' => [],
                    'rowCount' => 0
                ]
            );

            $this->bindParts[$part] = [[]];

            return $this->getQuery($dataSourceKey);
        }

        if (!is_array(reset($data))) {
            return $this->affect([$data], $part, $dataSourceKey);
        }

        $fieldNames = [];

        foreach (array_keys(reset($data)) as $fieldName) {
            $fieldNames[] = $modelClass::getFieldName($fieldName);
        }

        $this->sqlParts[$part] = array_merge(
            $this->sqlParts[$part],
            [
                'modelClass' => $modelClass,
                'fieldNames' => $fieldNames,
                'rowCount' => count($data)
            ]
        );


        $this->appendCacheTag($modelClass, $fieldNames, false, true);

        $this->bindParts[$part] = array_merge($this->bindParts[$part], $data);

        return $this->getQuery($dataSourceKey);
    }

    /**
     * Return query result for update query
     *
     * @param  array $data Key-value array
     * @param  null $dataSource
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function getUpdateQuery(array $data, $dataSource = null)
    {
        $this->queryType = QueryBuilder::TYPE_UPDATE;
        return $this->affect($data, QueryBuilder::PART_SET, $dataSource);
    }

    /**
     * Return query result for delete query
     *
     * @param  array $pkValues
     * @param  string|null $dataSourceKey
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
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
     * @param  array $value
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @return QueryBuilder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function inPk(array $value, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = $this->getModelClassTableAlias($modelTableData)[0];

        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

        if (count($pkFieldNames) > 1) {
           throw  new Error('not implemented');
        }
        
        return $this->in(reset($pkFieldNames), $value, $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression 'in (?)'
     *
     * @param  $fieldName
     * @param  array $fieldValue
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function in($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SQL_COMPARISON_KEYWORD_IN,
            $modelTableData,
            $isUse
        );
    }

    /**
     * Set flag of get count rows
     *
     * @param  array $fieldNameAlias
     * @param  array $modelTableData
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function count($fieldNameAlias = [], $modelTableData = [])
    {
        /**
         * @var Model $modelClass
         */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);
        list($fieldName, $fieldAlias) = $this->getFieldNameAlias($fieldNameAlias, $modelClass);
        $fieldNames = [];

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

        foreach ((array)$fieldName as $name) {
            $name = $fieldColumnMap[$name];

            $fieldNames[] = '`' . $tableAlias . '`.`' . $modelClass::getFieldName($name) . '`';
            $this->appendCacheTag($modelClass, $name, true, false);
        }

        if (!$fieldAlias) {
            $fieldAlias = strtolower($modelClass::getClassName()) . '__count';
        }

        $this->select('COUNT(' . implode(',', $fieldNames) . ')', $fieldAlias, [$modelClass => '']);

        return $this;
    }

    /**
     * Return couple fieldName and fieldAlias
     *
     * @param  string|array $fieldNameAlias
     * @param  Model $modelClass
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
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

            list($fieldName, $fieldAlias) = each($fieldNameAlias);

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
     * Ascending ordering
     *
     * @param  $fieldName
     * @param  array $modelTableData
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function asc($fieldName = '/pk', $modelTableData = [])
    {
        return $this->order($fieldName, QueryBuilder::SQL_ORDERING_ASC, $modelTableData);
    }

    /**
     * Ordering
     *
     * @param  $fieldName
     * @param  $ascOrDesc
     * @param  array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
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

        $fieldName = $modelClass::getFieldName($fieldName);

        if (!isset($this->sqlParts[self::PART_ORDER][$modelClass])) {
            $this->sqlParts[self::PART_ORDER][$modelClass] = [
                $tableAlias, [
                    $fieldName => $ascOrDesc
                ]
            ];
        } else {
            $this->sqlParts[self::PART_ORDER][$modelClass][1][$fieldName] = $ascOrDesc;
        }

        return $this;
    }

    /**
     * grouping by
     *
     * @param  $fieldName
     * @param  array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function group($fieldName = null, $modelTableData = [])
    {
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

        if (!isset($this->sqlParts[self::PART_GROUP][$modelClass])) {
            $this->sqlParts[self::PART_GROUP][$modelClass] = [
                $tableAlias, [$fieldName]
            ];
        } else {
            $this->sqlParts[self::PART_GROUP][$modelClass][1][] = $fieldName;
        }

        return $this;
    }

    /**
     * Descending ordering
     *
     * @param  $fieldName
     * @param  array $modelTableData
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function desc($fieldName = '/pk', $modelTableData = [])
    {
        return $this->order($fieldName, QueryBuilder::SQL_ORDERING_DESC, $modelTableData);
    }

    /**
     * Execute query create table
     *
     * @param  string|null $dataSourceKey
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.2
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
     * @param  array $scheme
     * @param  null $modelClass
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
     * @param  string|null $dataSourceKey
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.2
     */
    public function dropTableQuery($dataSourceKey = null)
    {
        $this->queryType = QueryBuilder::TYPE_DROP;
        $this->sqlParts[self::PART_DROP]['_drop'] = $this->modelClass;
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
     * @param  array $modelTableData
     * @param  string $sqlLogical
     * @param bool $isUse
     * @return QueryBuilder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function search($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND, $isUse = true)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SEARCH_KEYWORD,
            $modelTableData,
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
        
        $this->triggers[$type][] = [$trigger . 'Trigger', $params, $modelClass];
        
        return $this;
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
                $widget->queryBuilderPart($this, $widget->getValues());
            }

            $this->widgets[] = $widget;
        }
        
        return $this;
    }

    public function orderWidget($widgetName, $key, $value, $fieldName = null, $modelTableData = [])
    {
        $widget = $this->widgets[$widgetName]->bind([$key => $value]);
        $value = $widget->getValue($key);

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
     * @param $page
     * @param $limit
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function setPagination($page, $limit)
    {
        if (empty($page)) {
            $page = 1;
        }

        if (!isset($limit)) {
            $limit = 0;
        }

        return $this
            ->setCalcFoundRows()
            ->limit($limit, ($page - 1) * $limit);
    }

    /**
     * Set query part limit
     *
     * @param  $limit
     * @param  int|null $offset
     * @return QueryBuilder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function limit($limit, $offset = 0)
    {
        $this->sqlParts[self::PART_LIMIT] = [
            'limit' => $limit,
            'offset' => $offset
        ];

        return $this;
    }


    public function isCalcFoundRows() {
        return $this->sqlParts[self::PART_SELECT]['_calcFoundRows'];
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

    public function filterWidget(
        $widgetName,
        $key,
        $value,
        $fieldName = null,
        $comparison = QueryBuilder::SQL_COMPARISON_OPERATOR_EQUAL,
        $modelTableData = [],
        $isUse = true
    )
    {
        /** @var Form $widget */
        $widget = $this->widgets[$widgetName]->bind([$key => $value]);

        $value = $widget->getValue($key);

        if (!empty($value)) {
            if (!$fieldName) {
                $fieldName = $key;
            }

            $this->where(QueryBuilder::SQL_LOGICAL_AND, [$fieldName => $value], $comparison, $modelTableData, $isUse);
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
     * @param argumentString
     * @param array $modelTableData
     * @return $this
     */
    public function func($funcName, $argumentString, $modelTableData = [])
    {
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
            $fieldAlias = strtolower($modelClass::getClassName()) . '__' . strtolower($funcName);
        }

        $modelScheme = $modelClass::getScheme();

        $fieldColumns = $modelScheme->getFieldColumnMap();

        $this->select(
            ($fieldName ? strtoupper($fieldName) : '') . '(' .
            (isset($fieldColumns[$argumentString]) ? $tableAlias . '.' . $fieldColumns[$argumentString] : $argumentString) .
            ')',
            $fieldAlias,
            [$modelClass => '']
        );

        return $this;
    }

    /**
     * @param $part
     * @param $path
     *
     * @todo not tested
     *
     * @return QueryBuilder
     */
    public function reset($part, $path)
    {
        $values = &$this->sqlParts;

        foreach ($path as $key) {
            $values = &$values[$part];
            $part = $key;
        }

        unset($values[$part]);

        return $this;
    }

    private function getBindParts()
    {
        return $this->bindParts;
    }

    public function havingLike($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        return $this->havingPart(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SQL_COMPARISON_KEYWORD_LIKE,
            $modelTableData
        );
    }

    public function havingGt($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = QueryBuilder::SQL_LOGICAL_AND)
    {
        return $this->havingPart(
            $sqlLogical,
            [$fieldName => $fieldValue],
            QueryBuilder::SQL_COMPARISON_OPERATOR_GREATER,
            $modelTableData
        );
    }

    private function havingPart($sqlLogical, array $fieldNameValues, $sqlComparison, $modelTableData = [])
    {
        /**
         * @var Model $modelClass
         */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        foreach ($fieldNameValues as $fieldName => $value) {
            $fieldName = $modelClass::getFieldName($fieldName);

            if ($value instanceof Model) {
                $value = $value->getPk();
                $fieldName .= '__fk';
            }

            $having = [$sqlLogical, $fieldName, $sqlComparison, count((array)$value)];

            if (isset($this->sqlParts[QueryBuilder::PART_HAVING][$tableAlias])) {
                $this->sqlParts[QueryBuilder::PART_HAVING][$tableAlias]['data'][] = $having;
            } else {
                $this->sqlParts[QueryBuilder::PART_HAVING][$tableAlias] = [
                    'class' => $modelClass,
                    'data' => [$having]
                ];
            }

            $this->appendCacheTag($modelClass, $fieldName, true, false);

            $this->bindParts[QueryBuilder::PART_HAVING][] = $value;
        }

        return $this;
    }

    /**
     * @param $scope
     * @param array $data
     * @param Model $modelClass
     * @return $this
     */
    public function scope($scope, array $data = [], $modelClass = null)
    {
        $modelClass = $modelClass
            ? Model::getClass($modelClass)
            : $this->getModelClass();

        $modelClass::getQueryScope()->$scope($this, $data);

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
}
