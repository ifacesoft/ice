<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 30.12.13
 * Time: 19:00
 */

namespace ice\core;

use ice\Exception;

class Query
{
    const SQL_STATEMENT_SELECT = 'SELECT';
    const SQL_STATEMENT_INSERT = 'INSERT';
    const SQL_STATEMENT_UPDATE = 'UPDATE';
    const SQL_STATEMENT_DELETE = 'DELETE';
    const SQL_CLAUSE_FROM = 'FROM';
    const SQL_CLAUSE_INTO = 'INTO';
    const SQL_CLAUSE_VALUES = 'VALUES';
    const SQL_CLAUSE_INNER_JOIN = 'INNER JOIN';
    const SQL_CLAUSE_LEFT_JOIN = 'LEFT JOIN';
    const CLAUSE_WHERE_LOGICAL_OPERATOR = 'lo';
    const CLAUSE_WHERE_FIELD_NAME = 'fn';
    const CLAUSE_WHERE_COMPARSION_OPERATOR = 'co';
    const CLAUSE_WHERE_FIELD_VALUE = 'fv';
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
    const SQL_COMPARSION_KEYWORD_IN = 'IN';
    const SQL_COMPARSION_KEYWORD_BETWEEN = 'BETWEEN';
    const SQL_COMPARSION_KEYWORD_IS_NULL = 'IS NULL';
    const SQL_COMPARSION_KEYWORD_IS_NOT_NULL = 'IS NOT NULL';


    const ASC = 'ASC';
    const DELETE = 'DELETE';
    const INSERT = 'INSERT';
    const VALUES = 'VALUES';
    const REPLACE = 'REPLACE';
    const SHOW = 'SHOW';
    const UPDATE = 'UPDATE';
    const SET = 'SET';
    const DESC = 'DESC';
    const DISTINCT = 'DISTINCT';
    const EXPLAIN = 'EXPLAIN';
    const FROM = 'FROM';
    const GROUP = 'GROUP';
    const HAVING = 'HAVING';
    const INDEX = 'INDEX';
    const INDEXES = 'INDEXES';
    const INNER_JOIN = 'INNER JOIN';
    const JOIN = 'JOIN';
    const LEFT_JOIN = 'LEFT JOIN';
    const RIGHT_JOIN = 'RIGHT JOIN';
    const ORDER = 'ORDER';
    const SELECT = 'SELECT';
    const TABLE = 'TABLE';
    const TYPE = 'TYPE';
    const LIMIT_COUNT = 'LIMITCOUNT';
    const LIMIT_OFFSET = 'LIMITOFFSET';
    const VALUE = 'VALUE';
    const WHERE = 'WHERE';

    const SQL_OR = 'OR';
    const USE_INDEX = 'USE INDEX';
    const FORCE_INDEX = 'FORCE INDEX';
    const CALC_FOUND_ROWS = 'CALC_FOUND_ROWS';
    const BIND = 'BIND';

    private $_result = null;
    private $_statementType = null;
    private $_select = array();
    private $_where = array();
    private $_limit = array();
    private $_join = array();
    private $_values = array();
    private $_set = array();
    private $_selectCount = false;

    private $_modelClass = null;
    private $_tableAlias = null;

    private function __construct($statementType, $modelClass, $tableAlias)
    {
        $this->_statementType = $statementType;
        $this->_modelClass = $modelClass;
        if (!$tableAlias) {
            $this->_tableAlias = $modelClass::getModelName();
        }
    }

    public static function getInstance($statementType, $modelClass, $tableAlias = null)
    {
        return new Query($statementType, $modelClass, $tableAlias);
    }

    /**
     * @return string
     */
    public function getStatementType()
    {
        return $this->_statementType;
    }

    public function select($column, $alias = null)
    {
        if (empty($column)) {
            return $this;
        }

        /** @var Model $modelClass */
        $modelClass = $this->getModelClass();

        if ($column == '*') {
            $column = $modelClass::getFieldNames();
        }

        if (is_array($column)) {
            foreach ($column as $columnName => $alias) {
                if (is_string($columnName)) {
                    $this->select($columnName, $alias);
                } else {
                    $this->select($alias);
                }
            }

            return $this;
        }

        if (empty($this->_select)) {
            $pkName = $modelClass::getPkName();
            $this->_select[$pkName] = $pkName;
        }

        $column = $modelClass::getFieldName($column);

        if (!$alias) {
            $alias = $column;
        }

        $this->_select[$alias] = $column;

        return $this;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->_modelClass;
    }

    /**
     * @param Data_Source $dataSource
     * @return Data
     */
    public function execute(Data_Source $dataSource = null)
    {
        return $this->getDataSource($dataSource)->execute($this);
    }

    /**
     * @param Data_Source $dataSource
     * @return Data_Source
     */
    private function getDataSource(Data_Source $dataSource = null)
    {
        if (!$dataSource) {
            /** @var Model $modelName */
            $modelName = $this->getModelClass();

            $dataSource = $modelName::getDataSource();
        }

        return $dataSource;
    }

    public function notNull($fieldName, $sql_logical = Query::SQL_LOGICAL_AND)
    {
        /** @var Model $modelClass */
        $modelClass = $this->getModelClass();

        return $this->where(
            $sql_logical,
            $modelClass::getFieldName($fieldName),
            Query::SQL_COMPARSION_KEYWORD_IS_NOT_NULL
        );
    }

    /**
     * @param $sql_logical
     * @param $fieldName
     * @param $sql_comparsion
     * @param null $value
     * @return $this
     * @throws Exception
     */
    private function where($sql_logical, $fieldName, $sql_comparsion, $value = null)
    {
        if ($this->_result !== null) {
            throw new Exception('Запрос уже оттранслирован ранее. Внесение изменений в запрос не принесет никаких результатов');
        }

        $where = array(
            array(
                Query::CLAUSE_WHERE_LOGICAL_OPERATOR => $sql_logical,
                Query::CLAUSE_WHERE_FIELD_NAME => $fieldName,
                Query::CLAUSE_WHERE_COMPARSION_OPERATOR => $sql_comparsion
            ),
            $value
        );

        $this->_where[] = $where;

        return $this;
    }

    public function isNull($fieldName, $sql_logical = Query::SQL_LOGICAL_AND)
    {
        /** @var Model $modelClass */
        $modelClass = $this->getModelClass();

        return $this->where($sql_logical, $modelClass::getFieldName($fieldName), Query::SQL_COMPARSION_KEYWORD_IS_NULL);
    }

    public function ne($fieldName, $value, $sql_logical = Query::SQL_LOGICAL_AND)
    {
        /** @var Model $modelClass */
        $modelClass = $this->getModelClass();

        return $this->where(
            $sql_logical,
            $modelClass::getFieldName($fieldName),
            Query::SQL_COMPARSION_OPERATOR_NOT_EQUAL,
            $value
        );
    }

    public function pk($value, $sql_logical = Query::SQL_LOGICAL_AND)
    {
        return $this->eq('/pk', $value, $sql_logical);
    }

    public function eq($fieldName, $value, $sql_logical = Query::SQL_LOGICAL_AND)
    {
        /** @var Model $modelClass */
        $modelClass = $this->getModelClass();

        return $this->where(
            $sql_logical,
            $modelClass::getFieldName($fieldName),
            Query::SQL_COMPARSION_OPERATOR_EQUAL,
            $value
        );
    }

    public function in($fieldName, array $value, $sql_logical = Query::SQL_LOGICAL_AND)
    {
        if (empty($value)) {
            return $this;
        }

        if (count($value) == 1) {
            return $this->eq($fieldName, reset($value), $sql_logical);
        }

        /** @var Model $modelClass */
        $modelClass = $this->getModelClass();

        return $this->where(
            $sql_logical,
            $modelClass::getFieldName($fieldName),
            Query::SQL_COMPARSION_KEYWORD_IN,
            $value
        );
    }

    public function limit($limit, $offset = null)
    {
        $this->_limit = array($limit, $offset);
        return $this;
    }

    public function inner($modelClass)
    {
        return $this->join(Query::INNER_JOIN, $modelClass);
    }

    private function join($joinType, $modelClass, $tableAlias = null, $condition = null)
    {
        if ($this->_result !== null) {
            throw new Exception('Запрос уже оттранслирован ранее. Внесение изменений в запрос не принесет никаких результатов');
        }

        if (!$tableAlias) {
            $tableAlias = $modelClass::getModelName();
        }

        $currentJoin = array(
            'type' => $joinType,
            'class' => $modelClass,
            'alias' => $tableAlias
        );

        if (!$condition) {
            $modelColumnNames = $modelClass::getScheme()->getColumnNames();
            $modelName = $modelClass::getModelName();

            $joins = array(
                array(
                    'class' => $this->getModelClass(),
                    'alias' => $this->getTableAlias()
                ),
            );

            if (!empty($this->getJoin())) {
                $joins = array_merge($joins, $this->getJoin());
            }

            $joins[] = $currentJoin;

            foreach ($joins as $join) {
                $joinModelClass = $join['class'];
                $joinTableAlias = $join['alias'];
                $joinModelName = $joinModelClass::getModelName();

                if (in_array(strtolower($joinModelName . '__fk'), $modelColumnNames)) {
                    $condition = $tableAlias . '.' . strtolower($joinModelName) . '__fk = ' .
                        $joinTableAlias . '.' . strtolower($joinModelName) . '_pk';
                    break;
                }

                $joinModelColumnNames = $joinModelClass::getScheme()->getColumnNames();
                if (in_array(strtolower($modelName . '__fk'), $joinModelColumnNames)) {
                    $condition = $tableAlias . '.' . strtolower($modelName) . '_pk = ' .
                        $joinTableAlias . '.' . strtolower($modelName . '__fk');
                    break;
                }
            }

            if (!$condition) {
                throw new Exception('Could not defined condition for join part of sql query');
            }
        }

        $currentJoin['on'] = $condition;

        $this->_join[] = $currentJoin;

        return $this;
    }

    /**
     * @return string
     */
    public function getTableAlias()
    {
        return $this->_tableAlias;
    }

    /**
     * @return array
     */
    public function getJoin()
    {
        return $this->_join;
    }

    public function left($modelClass)
    {
        return $this->join(Query::LEFT_JOIN, $modelClass);
    }

    /**
     * @param array $values
     * @return Query
     */
    public function values(array $values)
    {
        if (is_array(reset($values))) {
            $this->_values += $values;
        } else {
            $this->_values[] = $values;
        }

        return $this;
    }

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }

            return $this;
        }

        if (empty($key)) {
            throw new Exception('Имя поля не может быть пустым');
        }

        $this->_set[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getSelect()
    {
        return $this->_select;
    }

    /**
     * @return array
     */
    public function getWhere()
    {
        return $this->_where;
    }

    /**
     * @return array
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * @return array
     */
    public function getSet()
    {
        return $this->_set;
    }

    public function __toString()
    {
        return print_r($this->getResult(), true);
    }

    public function getResult($dataSourceName = 'Mysqli')
    {
        if ($this->_result !== null) {
            return $this->_result;
        }

        $this->_result = Query_Translator::get('\ice\query\translator\\' . $dataSourceName)->translate($this);

        return $this->_result;
    }

    public function count()
    {
        $this->_selectCount = true;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSelectCount()
    {
        return $this->_selectCount;
    }


}