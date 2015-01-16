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
    const SQL_COMPARSION_OPERATOR_EQUAL = '=';
    const SQL_COMPARSION_OPERATOR_NOT_EQUAL = '<>';
    const SQL_COMPARSION_OPERATOR_LESS = '<';
    const SQL_COMPARSION_OPERATOR_GREATER = '>';
    const SQL_COMPARSION_OPERATOR_GREATER_OR_EQUAL = '>=';
    const SQL_COMPARSION_OPERATOR_LESS_OR_EQUAL = '<=';
    const SQL_COMPARSION_KEYWORD_LIKE = 'LIKE';
    const SQL_COMPARSION_KEYWORD_RLIKE = 'RLIKE';
    const SQL_COMPARSION_KEYWORD_RLIKE_REVERSE = 'RLIKE_REVERSE';
    const SQL_COMPARSION_KEYWORD_IN = 'IN';
    const SQL_COMPARSION_KEYWORD_NOT_IN = 'NOT IN';
    const SQL_COMPARSION_KEYWORD_BETWEEN = 'BETWEEN';
    const SQL_COMPARSION_KEYWORD_IS_NULL = 'IS NULL';
    const SQL_COMPARSION_KEYWORD_IS_NOT_NULL = 'IS NOT NULL';


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
        'validate' => [],
        'invalidate' => []
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

        if (empty($tableAlias)) {
            $tableAlias = Object::getName($modelClass);
        }

        $this->_tableAlias = $tableAlias;
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
    public static function getInstance($modelClass, $tableAlias = null)
    {
        return new Query_Builder($modelClass, $tableAlias);
    }

    /**
     * Set in query part where expression 'IS NOT NULL'
     *
     * @param $fieldName
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function notNull($fieldName, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            $fieldName,
            Query_Builder::SQL_COMPARSION_KEYWORD_IS_NOT_NULL,
            null,
            $modelClass,
            $tableAlias
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
     *                      Query::CLAUSE_WHERE_COMPARSION_OPERATOR => $sql_comparsion
     *                  ]
     *              ]
     *          ]
     *      ];
     * ```
     * @param $sqlLogical
     * @param $fieldName
     * @param $sqlComparsion
     * @param null $value
     * @param $modelClass
     * @param $tableAlias
     * @throws Exception
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function where($sqlLogical, $fieldName, $sqlComparsion, $value = null, $modelClass = null, $tableAlias = null)
    {
        $modelClass = !$modelClass
            ? $modelClass = $this->getModelClass()
            : Model::getClass($modelClass);

        if (!$tableAlias) {
            $tableAlias = $modelClass == $this->getModelClass()
                ? $this->getTableAlias()
                : Object::getName($modelClass);
        }

        $fieldName = $modelClass::getFieldName($fieldName);

        $where = [$sqlLogical, $fieldName, $sqlComparsion, count((array)$value)];

        if (isset($this->_sqlParts[Query_Builder::PART_WHERE][$modelClass])) {
            $this->_sqlParts[Query_Builder::PART_WHERE][$modelClass][1][] = $where;
        } else {
            $this->_sqlParts[Query_Builder::PART_WHERE][$modelClass] = [
                $tableAlias, [$where]
            ];
        }

        $this->appendCacheTag($modelClass, $fieldName, true, false);

        $this->_bindParts[Query_Builder::PART_WHERE][] = (array)$value;

        return $this;
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
     * Append cache validate or invalidate tags for this query builder
     *
     * @param $modelClass Model
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
        $fields = $modelClass::getMapping();

        foreach ((array)$fieldNames as $fieldName) {
            if (array_key_exists($fieldName, $fields)) {
                if ($isValidate) {
                    $this->_cacheTags['validate'][$modelClass][$fieldName] = true;
                }
                if ($isInvalidate) {
                    $this->_cacheTags['invalidate'][$modelClass][$fieldName] = true;
                }
            }
        }
    }

    /**
     * Set in query part where expression 'IS NULL'
     *
     * @param $fieldName
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function isNull($fieldName, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            $fieldName,
            Query_Builder::SQL_COMPARSION_KEYWORD_IS_NULL,
            null,
            $modelClass,
            $tableAlias
        );
    }

    /**
     * Set in query part where expression '= ?' for primary key column
     *
     * @param $pk
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @throws Exception
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function pk($pk, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        if (empty($pk)) {
            return $this;
        }

        if (!is_array($pk)) {
            if (!$modelClass) {
                $modelClass = $this->getModelClass();
            }

            $pkFieldNames = $modelClass::getPkFieldNames();
            $pk = [reset($pkFieldNames) => $pk];
        }

        return $this->eq($pk, $modelClass, $tableAlias, $sqlLogical);
    }

    /**
     * Set in query part where expression '= ?'
     *
     * @param array $fieldNameValues
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function eq(array $fieldNameValues, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        foreach ($fieldNameValues as $fieldName => $value) {
            if (is_array($value)) {
                return $this->in($fieldName, $value, $modelClass, $tableAlias, $sqlLogical);
            }

            if ($value instanceof Model) {
                $value = $value->getPk();
                $fieldName .= '__fk';
            }

            $this->where(
                $sqlLogical,
                $fieldName,
                Query_Builder::SQL_COMPARSION_OPERATOR_EQUAL,
                $value,
                $modelClass,
                $tableAlias
            );
        }

        return $this;
    }

    /**
     * Set in query part where expression 'in (?)'
     *
     * @param $fieldName
     * @param array $value
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function in($fieldName, array $value, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        if (empty($value)) {
            return $this;
        }

        if (count($value) == 1) {
            return $this->eq([$fieldName => reset($value)], $modelClass, $tableAlias, $sqlLogical);
        }

        return $this->where(
            $sqlLogical,
            $fieldName,
            Query_Builder::SQL_COMPARSION_KEYWORD_IN,
            $value,
            $modelClass,
            $tableAlias
        );
    }

    /**
     * Set in query part where expression '>= ?'
     *
     * @param $fieldName
     * @param $value
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function ge($fieldName, $value, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            $fieldName,
            Query_Builder::SQL_COMPARSION_OPERATOR_GREATER_OR_EQUAL,
            $value,
            $modelClass,
            $tableAlias
        );
    }

    /**
     * Set in query part where expression '<= ?'
     *
     * @param $fieldName
     * @param $value
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function le($fieldName, $value, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            $fieldName,
            Query_Builder::SQL_COMPARSION_OPERATOR_LESS_OR_EQUAL,
            $value,
            $modelClass,
            $tableAlias
        );
    }

    /**
     * Set in query part where expression '> ?'
     *
     * @param $fieldName
     * @param $value
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function gt($fieldName, $value, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            $fieldName,
            Query_Builder::SQL_COMPARSION_OPERATOR_GREATER,
            $value,
            $modelClass,
            $tableAlias
        );
    }

    /**
     * Set in query part where expression '< ?'
     *
     * @param $fieldName
     * @param $value
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function lt($fieldName, $value, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            $fieldName,
            Query_Builder::SQL_COMPARSION_OPERATOR_LESS,
            $value,
            $modelClass,
            $tableAlias
        );
    }

    /**
     * Set in query part where expression '= ""'
     *
     * @param $fieldName
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function isEmpty($fieldName, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->eq([$fieldName => ''], $modelClass, $tableAlias, $sqlLogical);
    }

    /**
     * Set in query part where expression '<> ""'
     *
     * @param $fieldName
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function notEmpty($fieldName, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->ne($fieldName, '', $modelClass, $tableAlias, $sqlLogical);
    }

    /**
     * Set in query part where expression '<> ?'
     *
     * @param $fieldName
     * @param $value
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function ne($fieldName, $value, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            $fieldName,
            Query_Builder::SQL_COMPARSION_OPERATOR_NOT_EQUAL,
            $value,
            $modelClass,
            $tableAlias
        );
    }

    /**
     * Set in query part where expression 'not in (?)'
     *
     * @param $fieldName
     * @param array $value
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function notIn($fieldName, array $value, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        if (empty($value)) {
            return $this;
        }

        if (count($value) == 1) {
            return $this->eq([$fieldName => reset($value)], $modelClass, $tableAlias, $sqlLogical);
        }

        return $this->where(
            $sqlLogical,
            $fieldName,
            Query_Builder::SQL_COMPARSION_KEYWORD_NOT_IN,
            $value,
            $modelClass,
            $tableAlias
        );
    }

    /**
     * Set in query part where expression '== 1' is boolean true(1)
     *
     * @param $fieldName
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function is($fieldName, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->eq([$fieldName => 1], $modelClass, $tableAlias, $sqlLogical);
    }

    /**
     * Set in query part where expression '== 0' is boolean false(0)
     *
     * @param $fieldName
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function not($fieldName, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->eq([$fieldName => 0], $modelClass, $tableAlias, $sqlLogical);
    }

    /**
     * Set in query part where expression 'like ?'
     *
     * @param $fieldName
     * @param $value
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function like($fieldName, $value, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        return $this->where(
            $sqlLogical,
            $fieldName,
            Query_Builder::SQL_COMPARSION_KEYWORD_LIKE,
            $value,
            $modelClass,
            $tableAlias
        );
    }

    /**
     * Set in query part where expression 'rlike ?'
     *
     * @param $fieldName
     * @param $value
     * @param null $modelClass
     * @param null $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function rlike($fieldName, $value, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        if (!$modelClass) {
            $modelClass = $this->getModelClass();
        }

        $modelFields = $modelClass::getMapping();
        $fieldValue = $modelClass::getFieldName($value);

        /** check ability use pattern from field in base */
        return array_key_exists($fieldValue, $modelFields)
            ? $this->where($sqlLogical, $fieldValue, Query_Builder::SQL_COMPARSION_KEYWORD_RLIKE_REVERSE, $fieldName, $modelClass, $tableAlias)
            : $this->where($sqlLogical, $modelClass::getFieldName($fieldName), Query_Builder::SQL_COMPARSION_KEYWORD_RLIKE, $value, $modelClass, $tableAlias);
    }

    /**
     * Set inner join query part
     *
     * @param $modelClass
     * @param $fieldNames
     * @param null $tableAlias
     * @param null $condition
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function inner($modelClass, $fieldNames = '/pk', $tableAlias = null, $condition = null)
    {
        return $this->_select($fieldNames, null, $modelClass, $tableAlias)
            ->join(Query_Builder::SQL_CLAUSE_INNER_JOIN, $modelClass, $tableAlias, $condition);
    }

    /**
     * Set  *join query part
     *
     * @param $joinType
     * @param $modelClass Model
     * @param null $tableAlias
     * @param null $condition
     * @return Query_Builder
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function join($joinType, $modelClass, $tableAlias = null, $condition = null)
    {
        $modelClass = !$modelClass
            ? $modelClass = $this->getModelClass()
            : Model::getClass($modelClass);

        if (!$tableAlias) {
            $tableAlias = $modelClass == $this->getModelClass()
                ? $this->getTableAlias()
                : Object::getName($modelClass);
        }

        $currentJoin = [
            'type' => $joinType,
            'class' => $modelClass,
            'alias' => $tableAlias
        ];

        if (!$condition) {
            $modelName = Object::getName($modelClass);
            $fields = $modelClass::getMapping();
            $fieldNamesOnly = array_keys($fields);

            $joins = [['class' => $this->getModelClass(), 'alias' => Object::getName($this->getModelClass())]];

            if (!empty($this->_sqlParts[self::PART_JOIN])) {
                $joins = array_merge($joins, $this->_sqlParts[self::PART_JOIN]);
            }

            $joins[] = $currentJoin;

            foreach ($joins as $join) {
                /** @var Model $joinModelClass */
                $joinModelClass = $join['class'];
                $joinTableAlias = $join['alias'];

                $joinFieldNames = $joinModelClass::getMapping();
                $joinFieldNamesOnly = array_keys($joinFieldNames);

                $joinModelName = Object::getName($joinModelClass);

                $joinModelNameFk = strtolower($joinModelName . '__fk');
                $joinModelNamePk = strtolower($joinModelName) . '_pk';

                if (in_array($joinModelNameFk, $fieldNamesOnly)) {
                    $condition = $tableAlias . '.' . $fields[$joinModelNameFk] . ' = ' .
                        $joinTableAlias . '.' . $joinFieldNames[$joinModelNamePk];
                    break;
                }

                $modelNameFk = strtolower($modelName . '__fk');
                $modelNamePk = strtolower($modelName) . '_pk';

                if (in_array($modelNameFk, $joinFieldNamesOnly)) {
                    $condition = $tableAlias . '.' . $fields[$modelNamePk] . ' = ' .
                        $joinTableAlias . '.' . $joinFieldNames[$modelNameFk];
                    break;
                }
            }

            if (!$condition) {
                throw new Exception('Could not defined condition for join part of sql query', $this->_sqlParts);
            }
        }

        $currentJoin['on'] = $condition;

        $this->_sqlParts[self::PART_JOIN][] = $currentJoin;

        return $this;
    }

    /**
     * Prepare select query part
     *
     * @param $fieldName
     * @param $fieldAlias
     * @param $modelClass
     * @param $tableAlias
     * @return $this
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    private function _select($fieldName, $fieldAlias, $modelClass, $tableAlias)
    {
        $modelClass = !$modelClass
            ? $modelClass = $this->getModelClass()
            : Model::getClass($modelClass);

        if (!$tableAlias) {
            $tableAlias = $modelClass == $this->getModelClass()
                ? $this->getTableAlias()
                : Object::getName($modelClass);
        }

        if (!isset($this->_sqlParts[self::PART_SELECT][$modelClass])) {
            $pkFieldNames = $modelClass::getPkFieldNames();

            $this->_sqlParts[self::PART_SELECT][$modelClass] = [
                $this->getTableAlias(), array_combine($pkFieldNames, $pkFieldNames)
            ];
        }

        if ($fieldName == '*') {
            $fieldName = $modelClass::getFieldNames();
        }

        if (is_array($fieldName)) {
            foreach ($fieldName as $field => $fieldAlias) {
                if (is_numeric($field)) {
                    $this->_select($fieldAlias, null, $modelClass, $tableAlias);
                } else {
                    $this->_select($field, $fieldAlias, $modelClass, $tableAlias);
                }
            }

            return $this;
        } else {
            $fieldName = explode(',', $fieldName);

            if (count($fieldName) > 1) {
                $this->_select($fieldName, null, $modelClass, $tableAlias);

                return $this;
            } else {
                $fieldName = reset($fieldName);
            }
        }

        $fieldName = $modelClass::getFieldName($fieldName);

        if (!$fieldAlias) {
            $fieldAlias = $fieldName;
        }

        if (!isset($this->_sqlParts[self::PART_SELECT][$modelClass])) {
            $pkNames = $modelClass::getPkFieldNames();

            $this->_sqlParts[self::PART_SELECT][$modelClass] = [
                $tableAlias, array_combine($pkNames, $pkNames)
            ];
        }

        $this->_sqlParts[self::PART_SELECT][$modelClass][1][$fieldName] = $fieldAlias;

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
     * @param mixed $fieldName
     * @param null $fieldAlias
     * @param null $modelClass
     * @param null $tableAlias
     * @param null $sourceName
     * @param int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function select($fieldName, $fieldAlias = null, $modelClass = null, $tableAlias = null, $sourceName = null, $ttl = 3600)
    {
        $this->_queryType = Query_Builder::TYPE_SELECT;

        $this->_select($fieldName, $fieldAlias, $modelClass, $tableAlias);

        return Query_Result::getInstance([$this->getQuery($sourceName), $ttl]);
    }

    /**
     * Return instance of query by current query builder
     *
     * @param null $sourceName
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function getQuery($sourceName = null)
    {
        return Query::getInstance([$sourceName, $this->_queryType, $this->_sqlParts, $this->_modelClass, $this->_cacheTags])
            ->bind($this->_bindParts);
    }

    /**
     * Set inner join query part
     *
     * @param $modelClass
     * @param $fieldNames
     * @param null $tableAlias
     * @param null $condition
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function left($modelClass, $fieldNames = null, $tableAlias = null, $condition = null)
    {
        return $this->_select($fieldNames, null, $modelClass, $tableAlias)
            ->join(Query_Builder::SQL_CLAUSE_LEFT_JOIN, $modelClass, $tableAlias, $condition);
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
     *      ->values('name', 'Petya')
     *      ->values(['name' => 'Petya', 'surname' => 'Vasechkin'])
     *      ->values([
     *                  ['name' => 'Petya', 'surname' => 'Vasechkin'],
     *                  ['name' => 'Ivan', 'surname' => 'Petrov'],
     *      ])
     * ```
     *
     * @param array $data Key-value array
     * @param bool $update
     * @param null $dataSource
     * @param int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function insert(array $data, $update = false, $dataSource = null, $ttl = 3600)
    {
        $this->_queryType = Query_Builder::TYPE_INSERT;
        $this->_sqlParts[Query_Builder::PART_VALUES]['_update'] = $update;
        return $this->affect($data, Query_Builder::PART_VALUES, $dataSource, $ttl);
    }

    /**
     * Affect query
     *
     * @param array $data Key-value array
     * @param $part
     * @param $sourceName
     * @param $ttl
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.1
     */
    private function affect(array $data, $part, $sourceName, $ttl)
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

            return Query_Result::getInstance([$this->getQuery($sourceName), $ttl]);
        }

        if (!is_array(reset($data))) {
            return $this->affect([$data], $part, $sourceName, $ttl);
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

        return Query_Result::getInstance([$this->getQuery($sourceName), $ttl]);
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
    public function update(array $data, $dataSource = null, $ttl = 3600)
    {
        $this->_queryType = Query_Builder::TYPE_UPDATE;
        return $this->affect($data, Query_Builder::PART_SET, $dataSource, $ttl);
    }

    /**
     * Return query result for delete query
     *
     * @param array $pkValues
     * @param null $sourceName
     * @param int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function delete($pkValues = [], $sourceName = null, $ttl = 3600)
    {
        $this->_queryType = Query_Builder::TYPE_DELETE;
        $this->_sqlParts[Query_Builder::PART_WHERE]['_delete'] = $this->_modelClass;

        $this->inPk((array)$pkValues);

        return Query_Result::getInstance([$this->getQuery($sourceName), $ttl]);
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
     * @param Model $modelClass
     * @param string $tableAlias
     * @param string $sqlLogical
     * @return Query_Builder
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function inPk(array $value, $modelClass = null, $tableAlias = null, $sqlLogical = Query_Builder::SQL_LOGICAL_AND)
    {
        if (empty($value)) {
            throw new Exception('Primary key is empty');
        }

        return $this->in('/pk', $value, $modelClass, $tableAlias, $sqlLogical);
    }

    /**
     * Set flag of get count rows
     *
     * @param string $fieldName
     * @param null $fieldAlias
     * @param null $modelClass
     * @param null $tableAlias
     * @return Query_Builder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function count($fieldName = '/pk', $fieldAlias = null, $modelClass = null, $tableAlias = null)
    {
        if (!$modelClass) {
            $modelClass = $this->getModelClass();
        }

        $fieldName = $modelClass::getFieldName($fieldName);

        if (!$fieldAlias) {
            $fieldAlias = $fieldName . '_count';
        }

        $this->appendCacheTag($modelClass, $fieldName, true, false);

        $this->_select('count(' . $fieldName . ')', $fieldAlias, $modelClass, $tableAlias);

        return $this;
    }

    /**
     * Ascending ordering
     *
     * @param $fieldName
     * @param null $modelClass
     * @param null $tableAlias
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function asc($fieldName, $modelClass = null, $tableAlias = null)
    {
        return $this->order($fieldName, 'ASC', $modelClass, $tableAlias);
    }

    /**
     * Ordering
     *
     * @param $fieldName
     * @param $isAscending
     * @param $modelClass
     * @param $tableAlias
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function order($fieldName, $isAscending, $modelClass = null, $tableAlias = null)
    {
        if (is_array($fieldName)) {
            foreach ($fieldName as $name) {
                $this->order($name, $isAscending, $modelClass, $tableAlias);
            }

            return $this;
        }

        $modelClass = !$modelClass
            ? $modelClass = $this->getModelClass()
            : Model::getClass($modelClass);

        if (!$tableAlias) {
            $tableAlias = $modelClass == $this->getModelClass()
                ? $this->getTableAlias()
                : Object::getName($modelClass);
        }

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
     * @param null $modelClass
     * @param null $tableAlias
     * @return $this
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function group($fieldName, $modelClass = null, $tableAlias = null)
    {
        if (is_array($fieldName)) {
            foreach ($fieldName as $name) {
                $this->order($name, $modelClass, $tableAlias);
            }

            return $this;
        }

        $modelClass = !$modelClass
            ? $modelClass = $this->getModelClass()
            : Model::getClass($modelClass);

        if (!$tableAlias) {
            $tableAlias = $modelClass == $this->getModelClass()
                ? $this->getTableAlias()
                : Object::getName($modelClass);
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
     * @param null $modelClass
     * @param null $tableAlias
     * @return Query_Builder
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function desc($fieldName, $modelClass = null, $tableAlias = null)
    {
        return $this->order($fieldName, 'DESC', $modelClass, $tableAlias);
    }

    /**
     * Set Limits and offset by page and limit
     *
     * @param array $paginator
     * @return Query_Builder
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function setPaginator(array $paginator)
    {
        if (empty($paginator)) {
            $page = 1;
            $limit = 1000;
        } else {
            list($page, $limit) = $paginator;
        }

        return $this->calcFoundRows()
            ->limit($limit, ($page - 1) * $limit);
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
     * Execute query create table
     *
     * @param null $sourceName
     * @param int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function create($sourceName = null, $ttl = 3600)
    {
        $this->_queryType = Query_Builder::TYPE_CREATE;
        return Query_Result::getInstance([$this->getQuery($sourceName), $ttl]);
    }

    /**
     * Execute query drop table
     *
     * @param null $sourceName
     * @param int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function drop($sourceName = null, $ttl = 3600)
    {
        $this->_queryType = Query_Builder::TYPE_DROP;
        $this->_sqlParts[self::PART_DROP]['_drop'] = $this->_modelClass;
        return Query_Result::getInstance([$this->getQuery($sourceName), $ttl]);
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
}