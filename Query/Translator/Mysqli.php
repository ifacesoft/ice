<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 19.01.14
 * Time: 18:00
 */

namespace ice\query\translator;

use ice\core\helper\Data_Mapping;
use ice\core\Model;
use ice\core\Query;
use ice\core\Query_Translator;
use ice\Exception;

class Mysqli extends Query_Translator
{
    protected function select(Query $query)
    {
        $sql = '';
        $binds = array();

        $sql .= $this->translateSelect(
            array($query->getModelClass(), $query->getTableAlias()),
            $query->getSelect(),
            $query->isSelectCount()
        );

        $sql .= $this->translateJoin($query->getJoin());

        list($whereSql, $whereBinds) = $this->translateWhere($query->getWhere());

        $sql .= $whereSql;
        $binds = array_merge($binds, $whereBinds);

        if (empty(trim($sql))) {
            throw new Exception('Запрос не должен быть пустым');
        }

        return array($sql, $binds);
    }

    private function translateSelect($from, array $select, $isSelectCount)
    {
        if (empty($select) && !$isSelectCount) {
            return '';
        }

        list($fromClass, $tableAlias) = $from;

        if ($isSelectCount) {
            $select = array('count(*)');
        }

        $sql = "\n" . Query::SQL_STATEMENT_SELECT .
            "\n\t" . implode(',' . "\n\t", $select);
        $sql .= "\n" . Query::SQL_CLAUSE_FROM .
            "\n\t" . Data_Mapping::getTableNameByClass($fromClass) . ' AS ' . $tableAlias;

        return $sql;
    }

    private function translateJoin(array $join)
    {
        $sql = '';

        if (empty($join)) {
            return $sql;
        }

        foreach ($join as $joinTable) {
            $sql .= "\n" . $joinTable['type'] . "\n\t" .
                Data_Mapping::getTableNameByClass($joinTable['class']) . ' AS ' . $joinTable['alias'] .
                ' ON (' . $joinTable['on'] . ')';
        }

        return $sql;
    }

    private function translateWhere(array $where)
    {
        $sql = '';
        $binds = array();

        if (empty($where)) {
            return array($sql, $binds);
        }

        foreach ($where as list($part, $bind)) {
            $whereQuery = null;

            switch ($part[Query::CLAUSE_WHERE_COMPARSION_OPERATOR]) {
                case Query::SQL_COMPARSION_OPERATOR_EQUAL:
                    $whereQuery = $part[Query::CLAUSE_WHERE_FIELD_NAME] . ' ' . Query::SQL_COMPARSION_OPERATOR_EQUAL . ' ?';
                    break;
                case Query::SQL_COMPARSION_OPERATOR_NOT_EQUAL:
                    $whereQuery = $part[Query::CLAUSE_WHERE_FIELD_NAME] . ' ' . Query::SQL_COMPARSION_OPERATOR_NOT_EQUAL . ' ?';
                    break;
                case Query::SQL_COMPARSION_KEYWORD_IN:
                    $whereQuery = $part[Query::CLAUSE_WHERE_FIELD_NAME] . ' IN (?' . str_repeat(
                            ',?',
                            count($bind) - 1
                        ) . ')';
                    break;
                case Query::SQL_COMPARSION_KEYWORD_IS_NULL:
                    $whereQuery = $part[Query::CLAUSE_WHERE_FIELD_NAME] . ' ' . Query::SQL_COMPARSION_KEYWORD_IS_NULL;
                    break;
                case Query::SQL_COMPARSION_KEYWORD_IS_NOT_NULL:
                    $whereQuery = $part[Query::CLAUSE_WHERE_FIELD_NAME] . ' ' . Query::SQL_COMPARSION_KEYWORD_IS_NOT_NULL;
                    break;
                default:
                    throw new Exception('Unknown comparsion operator');
            }

            $sql .= $sql ? ' ' . $part[Query::CLAUSE_WHERE_LOGICAL_OPERATOR] . "\n\t" : "\n" . 'WHERE' . "\n\t";
            $sql .= $whereQuery;

            if (is_array($bind)) {
                $binds += $bind;
            } else {
                $binds[] = $bind;
            }
        }

        return array($sql, $binds);
    }

    protected function insert(Query $query)
    {
        $sql = '';
        $binds = array();

        list($valuesSql, $valuesBinds) = $this->translateValues($query->getModelClass(), $query->getValues());

        $sql .= $valuesSql;
        $binds = array_merge($binds, $valuesBinds);

        if (empty(trim($sql))) {
            throw new Exception('Запрос не должен быть пустым');
        }

        return array($sql, $binds);
    }

    private function translateValues($insertClass, array $values)
    {
        $sql = '';
        $binds = array();

        if (empty($values)) {
            return array($sql, $binds);
        }

        if (count($values) == 1) {
            $value = array_filter(
                reset($values),
                function ($val) {
                    return $val !== null;
                }
            );

            $sql .= "\n" . Query::SQL_STATEMENT_INSERT . ' ' . Query::SQL_CLAUSE_INTO .
                "\n\t" . Data_Mapping::getTableNameByClass($insertClass);
            $sql .= "\n\t" . '(' . implode(',', array_keys($value)) . ')';
            $sql .= "\n" . Query::SQL_CLAUSE_VALUES;
            $sql .= "\n\t" . '(?' . str_repeat(',?', count($value) - 1) . ')';

            $binds += $value;

            return array($sql, $binds);
        }

        throw new Exception('need testing multi insert in one query');

        $sql .= "\n" . Query::SQL_STATEMENT_INSERT . ' ' . Query::SQL_CLAUSE_INTO .
            "\n\t" . Data_Mapping::getTableNameByClass($insertClass);
        $sql .= "\n\t" . '(' . implode(',', array_keys(reset($values))) . ')';
        $sql .= "\n" . Query::SQL_CLAUSE_VALUES;
        $sql .= "\n\t" . implode(
                ',' . "\n\t",
                array_map(
                    function ($value) {
                        return '(?' . str_repeat(',?', count($value) - 1) . ')';
                    },
                    $values
                )
            );

        foreach ($values as $value) {
            $binds += $value;
        }

        return array($sql, $binds);
    }

    protected function update(Query $query)
    {
        $sql = '';
        $binds = array();

        list($setSql, $setBinds) = $this->translateSet($query->getModelClass(), $query->getSet());

        $sql .= $setSql;
        $binds = array_merge($binds, $setBinds);

        list($whereSql, $whereBinds) = $this->translateWhere($query->getWhere());

        $sql .= $whereSql;
        $binds = array_merge($binds, $whereBinds);

        if (empty(trim($sql))) {
            throw new Exception('Запрос не должен быть пустым');
        }

        return array($sql, $binds);
    }

    private function translateSet($updateClass, array $set)
    {
        $sql = '';
        $binds = array();

        if (empty($set)) {
            return array($sql, $binds);
        }

        $sql .= "\n" . Query::SQL_STATEMENT_UPDATE .
            "\n\t" . Data_Mapping::getTableNameByClass($updateClass);
        $sql .= "\n" . 'SET';
        $sql .= "\n\t" . implode(
                ',' . "\n\t",
                array_map(
                    function ($value) {
                        return '' . $value . ' = ?';
                    },
                    array_keys($set)
                )
            );

        foreach ($set as $value) {
            $binds[] = $value;
        }

        return array($sql, $binds);
    }

    protected function delete(Query $query)
    {
        $sql = '';
        $binds = array();

        $sql .= $this->translateDelete($query->getModelClass());

        list($whereSql, $whereBinds) = $this->translateWhere($query->getWhere());

        $sql .= $whereSql;
        $binds = array_merge($binds, $whereBinds);

        return array($sql, $binds);
    }

    private function translateDelete($deleteClass)
    {
        return "\n" . Query::SQL_STATEMENT_DELETE . ' ' . Query::SQL_CLAUSE_FROM .
        "\n\t" . Data_Mapping::getTableNameByClass($deleteClass);
    }
}