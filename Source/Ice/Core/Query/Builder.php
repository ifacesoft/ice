<?php
/**
 * Ice core query builder class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Object;

/**
 * Class Query_Builder
 *
 * Core query builder class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
class Query_Builder
{
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
     * @var Model
     */
    private $_modelClass = null;

    /**
     * Table alias for prefix column in query
     *
     * @access private
     * @var null|string
     */
    private $_tableAlias = null;

    /**
     * Query statement type (SELECT|INSERT|UPDATE|DELETE)
     *
     * @access private
     * @var string
     */
    private $_queryType = null;

    /**
     * Query parts
     *
     * @access private
     * @var array
     */
    private $_sqlParts = [
        self::PART_CREATE => [],
        self::PART_DROP => [
            '_drop' => null
        ],
        self::PART_SELECT => [
            '_calcFoundRows' => null,
        ],
        self::PART_VALUES => [
            '_update' => null,
        ],
        self::PART_SET => [],
        self::PART_JOIN => [],
        self::PART_WHERE => [
            '_delete' => null,
        ],
        self::PART_GROUP => [],
        self::PART_ORDER => [],
        self::PART_LIMIT => []
    ];

    private $_triggers = [
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
     * Query binds
     *
     * @access private
     * @var array
     */
    private $_bindParts = [
        self::PART_VALUES => [],
        self::PART_SET => [],
        self::PART_WHERE => [],
        self::PART_LIMIT => []
    ];

    /**
     * Query caches tags (validates and invalidates)
     *
     * @access private
     * @var array
     */
    private $_cacheTags = [
        Cache::VALIDATE => [],
        Cache::INVALIDATE => []
    ];

    /**
     * Private constructor of query builder
     *
     * @param string $modelClass Class of model
     * @param string|null $tableAlias Alias of table in query (default: class name of model)
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct($modelClass, $tableAlias = null)
    {
        $this->_modelClass = Model::getClass($modelClass);

        if (!$tableAlias) {
            $tableAlias = $modelClass;
        }

        $this->_tableAlias = Object::getName($tableAlias);
    }

    /**
     * Return new instance of query builder
     *
     * @param string $modelClass Class of model
     * @param string|null $tableAlias Alias of table in query (default: class name of model)
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function create($modelClass, $tableAlias = null)
    {
        return new Query_Builder($modelClass, $tableAlias);
    }

    /**
     * Set in query part where expression 'IS NOT NULL'
     *
     * @param $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function notNull($fieldName, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => null],
            Query_Builder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL,
            $modelTableData
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
     * @param $sqlLogical
     * @param array $fieldNameValues
     * @param $sqlComparison
     * @param array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    private function where($sqlLogical, array $fieldNameValues, $sqlComparison, $modelTableData = [])
    {
        /** @var Model $modelClass */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        foreach ($fieldNameValues as $fieldName => $value) {
            $fieldName = $modelClass::getFieldName($fieldName);

            if ($value instanceof Model) {
                $value = $value->getPk();
                $fieldName .= '__fk';
            }

            $where = [$sqlLogical, $fieldName, $sqlComparison, count((array)$value)];

            if (isset($this->_sqlParts[Query_Builder::PART_WHERE][$modelClass])) {
                $this->_sqlParts[Query_Builder::PART_WHERE][$modelClass][1][] = $where;
            } else {
                $this->_sqlParts[Query_Builder::PART_WHERE][$modelClass] = [$tableAlias, [$where]];
            }

            $this->appendCacheTag($modelClass, $fieldName, true, false);

            $this->_bindParts[Query_Builder::PART_WHERE][] = $value == null
                ? [null]
                : (array)$value;
        }

        return $this;
    }

    /**
     * Return couple fieldName and fieldAlias
     *
     * @param string|array $fieldNameAlias
     * @param Model $modelClass
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.6
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

        if (!$fieldName || $fieldName == '/pk') {
            $fieldName = $modelClass::getScheme()->getPkFieldNames();
        }

        return [$fieldName, $fieldAlias];
    }

    /**
     * Check for model class and table alias
     *
     * @param $modelTableData
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.6
     */
    public function getModelClassTableAlias($modelTableData)
    {
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
            : Model::getClass($modelClass);

        if ($tableAlias === null) {
            $tableAlias = $modelClass;
        }

        if ($tableAlias !== '') {
            $tableAlias = Object::getName($tableAlias);
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
     * @since 0.0
     */
    public function getModelClass()
    {
        return $this->_modelClass;
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
     * @since 0.0
     */
    private function appendCacheTag($modelClass, $fieldNames, $isValidate, $isInvalidate)
    {
        $columnFieldMapping = $modelClass::getScheme()->getColumnFieldMap();

        foreach ((array)$fieldNames as $fieldName) {
            if (in_array($fieldName, $columnFieldMapping)) {
                if ($isValidate) {
                    $this->_cacheTags[Cache::VALIDATE][$modelClass][$fieldName] = true;
                }
                if ($isInvalidate) {
                    $this->_cacheTags[Cache::INVALIDATE][$modelClass][$fieldName] = true;
                }
            }
        }
    }

    /**
     * Return table alias for model class for query
     *
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getTableAlias()
    {
        return $this->_tableAlias;
    }

    /**
     * Set in query part where expression 'IS NULL'
     *
     * @param $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function isNull($fieldName, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => null],
            Query_Builder::SQL_COMPARISON_KEYWORD_IS_NULL,
            $modelTableData
        );
    }

    /**
     * Set in query part where expression '= ?' for primary key column
     *
     * @param $pk
     * @param $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function pk($pk, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        if (empty($pk)) {
            Logger::getInstance(__CLASS__)->exception('Primary key value mast be not empty', __FILE__, __LINE__, null, $this);
        }

        $eq = [];

        /** @var Model $modelClass */
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
     * @param array $fieldNameValues
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function eq(array $fieldNameValues, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            $fieldNameValues,
            Query_Builder::SQL_COMPARISON_OPERATOR_EQUAL,
            $modelTableData
        );
    }

    /**
     * Set in query part where expression 'in (?)'
     *
     * @param $fieldName
     * @param array $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function in($fieldName, array $fieldValue, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            Query_Builder::SQL_COMPARISON_KEYWORD_IN,
            $modelTableData
        );
    }

    /**
     * Set in query part where expression '>= ?'
     *
     * @param $fieldName
     * @param $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function ge($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            Query_Builder::SQL_COMPARISON_OPERATOR_GREATER_OR_EQUAL,
            $modelTableData
        );
    }

    /**
     * Set in query part where expression 'REGEXP ?'
     *
     * @param $fieldName
     * @param $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.4
     */
    public function regex($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            Query_Builder::SQL_COMPARISON_KEYWORD_REGEXP,
            $modelTableData
        );
    }

    /**
     * Set in query part where expression '<= ?'
     *
     * @param $fieldName
     * @param $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function le($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            Query_Builder::SQL_COMPARISON_OPERATOR_LESS_OR_EQUAL,
            $modelTableData
        );
    }

    /**
     * Set in query part where expression '> ?'
     *
     * @param $fieldName
     * @param $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function gt($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            Query_Builder::SQL_COMPARISON_OPERATOR_GREATER,
            $modelTableData
        );
    }

    /**
     * Set in query part where expression '< ?'
     *
     * @param $fieldName
     * @param $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function lt($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            Query_Builder::SQL_COMPARISON_OPERATOR_LESS,
            $modelTableData
        );
    }

    /**
     * Set in query part where expression '= ""'
     *
     * @param $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function isEmpty($fieldName, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->eq([$fieldName => ''], $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression '<> ""'
     *
     * @param $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function notEmpty($fieldName, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->ne([$fieldName => ''], $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression '<> ?'
     *
     * @param $fieldName
     * @param $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function ne($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            Query_Builder::SQL_COMPARISON_OPERATOR_NOT_EQUAL,
            $modelTableData
        );
    }

    /**
     * Set in query part where expression 'not in (?)'
     *
     * @param $fieldName
     * @param array $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function notIn($fieldName, array $fieldValue, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            Query_Builder::SQL_COMPARISON_KEYWORD_NOT_IN,
            $modelTableData
        );
    }

    /**
     * Set in query part where expression '== 1' is boolean true(1)
     *
     * @param $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function is($fieldName, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->eq([$fieldName => 1], $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression '== 0' is boolean false(0)
     *
     * @param $fieldName
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function not($fieldName, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->eq([$fieldName => 0], $modelTableData, $sqlLogical);
    }

    /**
     * Set in query part where expression 'like ?'
     *
     * @param $fieldName
     * @param $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function like($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            Query_Builder::SQL_COMPARISON_KEYWORD_LIKE,
            $modelTableData
        );
    }

    /**
     * Set in query part where expression 'rlike ?'
     *
     * @param $fieldName
     * @param $value
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function rlike($fieldName, $value, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        /** @var Model $modelClass */
        $modelClass = $this->getModelClassTableAlias($modelTableData)[0];

        $columnFieldMapping = $modelClass::getScheme()->getColumnFieldMap();
        $fieldValue = $modelClass::getFieldName($value);

        /** check ability use pattern from field in base */
        return in_array($fieldValue, $columnFieldMapping)
            ? $this->where($sqlLogical, [$fieldValue => $fieldName], Query_Builder::SQL_COMPARISON_KEYWORD_RLIKE_REVERSE, $modelTableData)
            : $this->where($sqlLogical, [$fieldName => $value], Query_Builder::SQL_COMPARISON_KEYWORD_RLIKE, $modelTableData);
    }

    /**
     * Set inner join query part
     *
     * @param $modelTableData
     * @param string $fieldNames
     * @param null $condition
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function inner($modelTableData, $fieldNames = '/pk', $condition = null)
    {
        return $this->_select($fieldNames, null, $modelTableData)
            ->join(Query_Builder::SQL_CLAUSE_INNER_JOIN, $modelTableData, $condition);
    }

    /**
     * @param $joinType
     * @param Model $modelClass
     * @param $tableAlias
     * @param Model $joinModelClass
     * @param $joinTableAlias
     * @return bool
     */
    private function addJoin($joinType, $modelClass, $tableAlias, $joinModelClass, $joinTableAlias)
    {
        if (isset($this->_sqlParts[self::PART_JOIN][$tableAlias])) {
            return false;
        }

        $joinModelScheme = $joinModelClass::getScheme();

        $oneToMany = $joinModelScheme->gets(Model_Scheme::ONE_TO_MANY);

        if (isset($oneToMany[$modelClass])) {
            $this->_sqlParts[self::PART_JOIN][$tableAlias] = [
                'type' => $joinType,
                'class' => $modelClass,
                'on' => $joinTableAlias . '.' . $oneToMany[$modelClass] . ' = ' . $tableAlias . '.' . $modelClass::getPkColumnName()
            ];

            return true;
        }

        $manyToOne = $joinModelScheme->gets(Model_Scheme::MANY_TO_ONE);

        if (isset($manyToOne[$modelClass])) {
            $this->_sqlParts[self::PART_JOIN][$tableAlias] = [
                'type' => $joinType,
                'class' => $modelClass,
                'on' => $tableAlias . '.' . $manyToOne[$modelClass] . ' = ' . $joinTableAlias . '.' . $joinModelClass::getPkColumnName()
            ];

            return true;
        }

        $manyToMany = $joinModelScheme->gets(Model_Scheme::MANY_TO_MANY);

        if (isset($manyToMany[$modelClass])) {
            return $this->addJoin($joinType, $manyToMany[$modelClass], Object::getName($manyToMany[$modelClass]), $joinModelClass, $joinTableAlias) &&
                $this->addJoin($joinType, $modelClass, $tableAlias, $manyToMany[$modelClass], Object::getName($manyToMany[$modelClass]));
        }

        $joinFieldNames = $joinModelClass::getScheme()->getFieldColumnMap();

        $joinModelName = Object::getName($joinModelClass);

        $joinModelNameFk = strtolower($joinModelName . '__fk');
        $joinModelNamePk = strtolower($joinModelName) . '_pk';

        if (in_array($joinModelNameFk, $modelClass::getScheme()->getFieldNames())) {
            $this->_sqlParts[self::PART_JOIN][$tableAlias] = [
                'type' => $joinType,
                'class' => $modelClass,
                'on' => $tableAlias . '.' . $modelClass::getScheme()->getFieldColumnMap()[$joinModelNameFk] . ' = ' . $joinTableAlias . '.' . $joinFieldNames[$joinModelNamePk]
            ];

            return true;
        }

        $modelName = Object::getName($modelClass);

        $modelNameFk = strtolower($modelName . '__fk');
        $modelNamePk = strtolower($modelName) . '_pk';

        if (in_array($modelNameFk, $joinModelScheme->getFieldNames())) {
            $this->_sqlParts[self::PART_JOIN][$tableAlias] = [
                'type' => $joinType,
                'class' => $modelClass,
                'on' => $tableAlias . '.' . $joinModelScheme->getFieldColumnMap()[$modelNamePk] . ' = ' . $joinTableAlias . '.' . $joinFieldNames[$modelNameFk]
            ];

            return true;
        }

        return false;
    }

    /**
     * Set  *join query part
     *
     * @param $joinType
     * @param array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @param null $condition
     * @return Query_Builder
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    private function join($joinType, $modelTableData, $condition = null)
    {
        /** @var Model $modelClass */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        if (!$condition) {
            $joins = array_merge(
                [$this->getTableAlias() => [
                    'type' => 'FROM',
                    'class' => $this->getModelClass()]
                ],
                $this->_sqlParts[self::PART_JOIN]
            );

            foreach ($joins as $joinTableAlias => $join) {
                if ($this->addJoin($joinType, $modelClass, $tableAlias, $join['class'], $joinTableAlias)) {
                    return $this;
                }
            }
        }

        if (!$condition) {
            Logger::getInstance(__CLASS__)->exception('Could not defined condition for join part of sql query', __FILE__, __LINE__, null, $this->_sqlParts);
        }

        $this->_sqlParts[self::PART_JOIN][$tableAlias] = [
            'type' => $joinType,
            'class' => $modelClass,
            'on' => $condition
        ];

        return $this;
    }

    /**
     * Prepare select query part
     *
     * @param $fieldName
     * @param $fieldAlias
     * @param array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.2
     */
    private function _select($fieldName, $fieldAlias, $modelTableData)
    {
        /** @var Model $modelClass */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        if ($tableAlias && !isset($this->_sqlParts[self::PART_SELECT][$modelClass][$tableAlias])) {
            $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

            $this->_sqlParts[self::PART_SELECT][$modelClass][$tableAlias] = array_combine($pkFieldNames, $pkFieldNames);
        }

        if ($fieldName == '*') {
            $fieldName = $modelClass::getScheme()->getFieldNames();
        }

        if ($fieldName == '/pk') {
            $fieldName = $modelClass::getScheme()->getPkFieldNames();
        }

        if (is_array($fieldName)) {
            foreach ($fieldName as $field => $fieldAlias) {
                if (is_numeric($field)) {
                    $this->_select($fieldAlias, null, $modelTableData);
                } else {
                    $this->_select($field, $fieldAlias, $modelTableData);
                }
            }

            return $this;
        } else {
            $fieldName = explode(', ', $fieldName);

            if (count($fieldName) > 1) {
                $this->_select($fieldName, null, $modelTableData);

                return $this;
            } else {
                $fieldName = reset($fieldName);
            }
        }

        $fieldName = $modelClass::getFieldName($fieldName);

        if (!$fieldAlias) {
            $fieldAlias = $fieldName;
        }

//        if (!isset($this->_sqlParts[self::PART_SELECT][$modelClass])) {
//            $pkNames = $modelClass::getScheme()->getPkFieldNames();
//
//            $this->_sqlParts[self::PART_SELECT][$modelClass] = [
//                $tableAlias, array_combine($pkNames, $pkNames)
//            ];
//        }

        $this->_sqlParts[self::PART_SELECT][$modelClass][$tableAlias][$fieldName] = $fieldAlias;

        $this->appendCacheTag($modelClass, $fieldName, true, false);

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
     * @param $fieldNames
     * @param array $modelTableData
     * @param string|null $dataSourceKey
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function select($fieldNames, $modelTableData = [], $dataSourceKey = null)
    {
        $this->_queryType = Query_Builder::TYPE_SELECT;

        $this->_select($fieldNames, null, $modelTableData);

        return $this->getQuery($dataSourceKey);
    }

    /**
     * Return instance of query by current query builder
     *
     * @param string|null $dataSourceKey
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function getQuery($dataSourceKey = null)
    {
        return Query::create([$dataSourceKey, $this->_queryType, $this->_sqlParts, $this->_modelClass, $this->_cacheTags, $this->_triggers])
            ->bind($this->_bindParts);
    }

    /**
     * Set inner join query part
     *
     * @param $modelTableData
     * @param string $fieldNames
     * @param null $condition
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function left($modelTableData, $fieldNames = '/pk', $condition = null)
    {
        return $this->_select($fieldNames, null, $modelTableData)
            ->join(Query_Builder::SQL_CLAUSE_LEFT_JOIN, $modelTableData, $condition);
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
     * @param int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function insert(array $data, $update = false, $dataSourceKey = null, $ttl = null)
    {
        $this->_queryType = Query_Builder::TYPE_INSERT;
        $this->_sqlParts[Query_Builder::PART_VALUES]['_update'] = $update;
        return $this->affect($data, Query_Builder::PART_VALUES, $dataSourceKey, $ttl);
    }

    /**
     * Affect query
     *
     * @param array $data Key-value array
     * @param $part
     * @param $dataSourceKey
     * @param $ttl
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.1
     */
    private function affect(array $data, $part, $dataSourceKey, $ttl)
    {
        $modelClass = $this->getModelClass();

        if (empty($data)) {
            $this->_sqlParts[$part] = array_merge(
                $this->_sqlParts[$part], [
                    'modelClass' => $modelClass,
                    'fieldNames' => [],
                    'rowCount' => 0
                ]
            );

            $this->_bindParts[$part] = [[]];

            return $this->getQuery($dataSourceKey)->getQueryResult($ttl);
        }

        if (!is_array(reset($data))) {
            return $this->affect([$data], $part, $dataSourceKey, $ttl);
        }

        $fieldNames = [];

        foreach (array_keys(reset($data)) as $fieldName) {
            $fieldNames[] = $modelClass::getFieldName($fieldName);
        }

        $this->_sqlParts[$part] = array_merge(
            $this->_sqlParts[$part], [
                'modelClass' => $modelClass,
                'fieldNames' => $fieldNames,
                'rowCount' => count($data)
            ]
        );

        $this->appendCacheTag($modelClass, $fieldNames, false, true);

        $this->_bindParts[$part] = array_merge($this->_bindParts[$part], $data);

        return $this->getQuery($dataSourceKey)->getQueryResult($ttl);
    }

    /**
     * Return query result for update query
     *
     * @param array $data Key-value array
     * @param null $dataSource
     * @param int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function update(array $data, $dataSource = null, $ttl = null)
    {
        $this->_queryType = Query_Builder::TYPE_UPDATE;
        return $this->affect($data, Query_Builder::PART_SET, $dataSource, $ttl);
    }

    /**
     * Return query result for delete query
     *
     * @param array $pkValues
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function delete($pkValues = [], $dataSourceKey = null, $ttl = null)
    {
        $this->_queryType = Query_Builder::TYPE_DELETE;
        $this->_sqlParts[Query_Builder::PART_WHERE]['_delete'] = $this->_modelClass;

        $this->inPk((array)$pkValues);

        return $this->getQuery($dataSourceKey)->getQueryResult($ttl);
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
     * @return Query_Builder
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function inPk(array $value, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        /** @var Model $modelClass */
        $modelClass = $this->getModelClassTableAlias($modelTableData)[0];

        $pkFieldNames = $modelClass::getScheme()->getPkFieldNames();

        return $this->in(reset($pkFieldNames), $value, $modelTableData, $sqlLogical);
    }

    /**
     * Set flag of get count rows
     *
     * @param array $fieldNameAlias
     * @param array $modelTableData
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function count($fieldNameAlias = [], $modelTableData = [])
    {
        /** @var Model $modelClass */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);
        list($fieldName, $fieldAlias) = $this->getFieldNameAlias($fieldNameAlias, $modelClass);
        $fieldNames = [];

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

        foreach ((array) $fieldName as $name) {
            $name = $fieldColumnMap[$name];

            $fieldNames[] = '`' . $tableAlias . '`.`' . $modelClass::getFieldName($name) . '`';
            $this->appendCacheTag($modelClass, $name, true, false);
        }

        if (!$fieldAlias) {
            $fieldAlias = strtolower($modelClass::getClassName()) . '__count';
        }

        $this->_select('count(' . implode(',', $fieldNames) . ')', $fieldAlias, [$modelClass => '']);

        return $this;
    }

    /**
     * Ascending ordering
     *
     * @param $fieldName
     * @param array $modelTableData
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function asc($fieldName, $modelTableData = [])
    {
        return $this->order($fieldName, Query_Builder::SQL_ORDERING_ASC, $modelTableData);
    }

    /**
     * Ordering
     *
     * @param $fieldName
     * @param $isAscending
     * @param array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    private function order($fieldName, $isAscending, $modelTableData = [])
    {
        if (is_array($fieldName)) {
            foreach ($fieldName as $name) {
                $this->order($name, $isAscending, $modelTableData);
            }

            return $this;
        }

        /** @var Model $modelClass */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        $fieldName = $modelClass::getFieldName($fieldName);

        if (!isset($this->_sqlParts[self::PART_ORDER][$modelClass])) {
            $this->_sqlParts[self::PART_ORDER][$modelClass] = [
                $tableAlias, [
                    $fieldName => $isAscending
                ]
            ];
        } else {
            $this->_sqlParts[self::PART_ORDER][$modelClass][1][$fieldName] = $isAscending;
        }

        return $this;
    }

    /**
     * grouping by
     *
     * @param $fieldName
     * @param array|string $modelTableData Key -> modelClass, value -> tableAlias
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function group($fieldName = null, $modelTableData = [])
    {
        if (is_array($fieldName)) {
            foreach ($fieldName as $name) {
                $this->group($name, $modelTableData);
            }

            return $this;
        }

        /** @var Model $modelClass */
        list($modelClass, $tableAlias) = $this->getModelClassTableAlias($modelTableData);

        if (!$fieldName || $fieldName == '/pk') {
            $this->group($modelClass::getScheme()->getPkFieldNames(), $modelTableData);

            return $this;
        }

        $fieldName = $modelClass::getFieldName($fieldName);

        if (!isset($this->_sqlParts[self::PART_GROUP][$modelClass])) {
            $this->_sqlParts[self::PART_GROUP][$modelClass] = [
                $tableAlias, [$fieldName]
            ];
        } else {
            $this->_sqlParts[self::PART_GROUP][$modelClass][1][] = $fieldName;
        }

        return $this;
    }

    /**
     * Descending ordering
     *
     * @param $fieldName
     * @param array $modelTableData
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function desc($fieldName, $modelTableData = [])
    {
        return $this->order($fieldName, Query_Builder::SQL_ORDERING_DESC, $modelTableData);
    }

    /**
     * Set Limits and offset by page and limit
     *
     * @param array $paginator
     * @return Query_Builder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function setPagination(array $paginator)
    {
        $page = isset($paginator['page']) ? $paginator['page'] : 1;
        $limit = isset($paginator['limit']) ? $paginator['limit'] : 10000;

        return $this->calcFoundRows()->limit($limit, ($page - 1) * $limit);
    }

    /**
     * Set query part limit
     *
     * @param $limit
     * @param int|null $offset
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function limit($limit, $offset = 0)
    {
        $this->_sqlParts[self::PART_LIMIT] = [$limit, $offset];

        return $this;
    }

    /**
     * Set flag for total found rows query
     *
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function calcFoundRows()
    {
        $this->_sqlParts[self::PART_SELECT]['_calcFoundRows'] = true;
        return $this;
    }

    /**
     * Execute query create table
     *
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function createTable($dataSourceKey = null, $ttl = null)
    {
        $modelClass = $this->_modelClass;

        foreach ($modelClass::getConfig()->gets('columns') as $columnName => $column) {
            $this->column($columnName, $column['scheme']);
        }

        $this->_queryType = Query_Builder::TYPE_CREATE;
        return $this->getQuery($dataSourceKey)->getQueryResult($ttl);
    }

    /**
     * Set column part for create or alter table
     *
     * @param $name
     * @param array $scheme
     * @param null $modelClass
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function column($name, array $scheme, $modelClass = null)
    {
        if (!$modelClass) {
            $modelClass = $this->getModelClass();
        }

        if (isset($this->_sqlParts[Query_Builder::PART_CREATE][$modelClass])) {
            $this->_sqlParts[Query_Builder::PART_CREATE][$modelClass][$name] = $scheme;
        } else {
            $this->_sqlParts[Query_Builder::PART_CREATE][$modelClass] = [$name => $scheme];
        }
        return $this;
    }

    /**
     * Execute query drop table
     *
     * @param string|null $dataSourceKey
     * @param int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function drop($dataSourceKey = null, $ttl = null)
    {
        $this->_queryType = Query_Builder::TYPE_DROP;
        $this->_sqlParts[self::PART_DROP]['_drop'] = $this->_modelClass;
        return $this->getQuery($dataSourceKey)->getQueryResult($ttl);
    }

    /**
     * Clone current query builder
     *
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function cloneBuilder()
    {
        return clone $this;
    }

    /**
     * Set in query part where expression for search
     *
     * @param $fieldName
     * @param $fieldValue
     * @param array $modelTableData
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public function search($fieldName, $fieldValue, $modelTableData = [], $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            [$fieldName => $fieldValue],
            Query_Builder::SEARCH_KEYWORD,
            $modelTableData
        );
    }

    public function afterSelect($method, $params = null)
    {
        return $this->addTrigger('afterSelect', $method, $params);
    }

    public function beforeSelect($method, $params = null)
    {
        return $this->addTrigger('beforeSelect', $method, $params);
    }

    public function afterInsert($method, $params = null)
    {
        return $this->addTrigger('afterInsert', $method, $params);
    }

    public function beforeInsert($method, $params = null)
    {
        return $this->addTrigger('beforeInsert', $method, $params);
    }

    public function afterUpdate($method, $params = null)
    {
        return $this->addTrigger('afterUpdate', $method, $params);
    }

    public function beforeUpdate($method, $params = null)
    {
        return $this->addTrigger('beforeUpdate', $method, $params);
    }

    public function afterDelete($method, $params = null)
    {
        return $this->addTrigger('afterDelete', $method, $params);
    }

    public function beforeDelete($method, $params = null)
    {
        return $this->addTrigger('beforeDelete', $method, $params);
    }

    private function addTrigger($type, $method, $params)
    {
        $this->_triggers[$type][] = [$method, $params];
        return $this;
    }
}