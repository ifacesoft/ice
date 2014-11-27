<?php
/**
 * Ice query translator implementation mysqli class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Query\Translator;

use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Query_Builder;
use Ice\Core\Query_Translator;

/**
 * Class Mysqli
 *
 * Translate with query translator mysqli
 *
 * @see Ice\Core\Query_Translator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Query_Translator
 *
 * @version 0.0
 * @since 0.0
 */
class Mysqli extends Query_Translator
{
    const SQL_CALC_FOUND_ROWS = 'SQL_CALC_FOUND_ROWS';
    const SQL_STATEMENT_SELECT = 'SELECT';
    const SQL_STATEMENT_INSERT = 'INSERT';
    const SQL_STATEMENT_UPDATE = 'UPDATE';
    const SQL_STATEMENT_DELETE = 'DELETE';
    const SQL_CLAUSE_FROM = 'FROM';
    const SQL_CLAUSE_INTO = 'INTO';
    const SQL_CLAUSE_SET = 'SET';
    const SQL_CLAUSE_VALUES = 'VALUES';
    const SQL_CLAUSE_WHERE = 'WHERE';
    const SQL_CLAUSE_GROUP = 'GROUP';
    const SQL_CLAUSE_ORDER = 'ORDER';
    const SQL_CLAUSE_LIMIT = 'LIMIT';
    const ON_DUPLICATE_KEY_UPDATE = 'ON DUPLICATE KEY UPDATE';

    /**
     * Translate query parts to sql string
     *
     * @param array $sqlParts
     * @return string
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function translate(array $sqlParts)
    {
        $sql = '';

        foreach ($sqlParts as $sqlPart => $data) {
            if (empty($data)) {
                continue;
            }

            $translate = 'translate' . ucfirst($sqlPart);
            $sql .= $this->$translate($data);
        }

        $sql = trim($sql);

        if (empty($sql)) {
            throw new Exception('Sql query is empty');
        }

        return $sql;
    }

    /**
     * Translate set part
     *
     * @param array $data
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    private function translateSet(array $data)
    {
        /** @var Model $modelClass */
        $modelClass = $data['modelClass'];

        $modelMapping = $modelClass::getMapping();

        if ($data['rowCount'] > 1) {
            $data['_update'] = true;
            return $this->translateValues($data);
        }

        $sql = "\n" . self::SQL_STATEMENT_UPDATE .
            "\n\t" . $modelClass::getTableName();
        $sql .= "\n" . self::SQL_CLAUSE_SET;
        $sql .= "\n\t" . '`' . implode('`=?,`', array_map(function ($fieldName) use ($modelMapping) {
                return $modelMapping[$fieldName];
            }, $data['fieldNames'])) . '`=?';

        return $sql;
    }

    /**
     * Translate values part
     *
     * @param array $data
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    private function translateValues(array $data)
    {
        $update = $data['_update'];
        unset($data['_update']);

        if (empty($data)) {
            return '';
        }

        /** @var Model $modelClass */
        $modelClass = $data['modelClass'];

        $sql = "\n" . self::SQL_STATEMENT_INSERT . ' ' . self::SQL_CLAUSE_INTO .
            "\n\t" . $modelClass::getTableName();

        $fieldNamesCount = count($data['fieldNames']);

        /** Insert empty row */
        if (!$fieldNamesCount) {
            $sql .= "\n\t" . '()';
            $sql .= "\n" . self::SQL_CLAUSE_VALUES;
            $sql .= "\n\t" . '()';

            return $sql;
        }

        $modelMapping = $modelClass::getMapping();

        $sql .= "\n\t" . '(`' . implode('`,`', array_map(function ($fieldName) use ($modelMapping) {
                return $modelMapping[$fieldName];
            }, $data['fieldNames'])) . '`)';
        $sql .= "\n" . self::SQL_CLAUSE_VALUES;

        $values = "\n\t" . '(?' . str_repeat(',?', $fieldNamesCount - 1) . ')';

        $sql .= $values;

        if ($data['rowCount'] > 1) {
            $sql .= str_repeat(',' . $values, $data['rowCount'] - 1);
        }

        if ($update) {
            $sql .= "\n" . self::ON_DUPLICATE_KEY_UPDATE;
            $sql .= implode(',', array_map(function ($fieldName) use ($modelMapping) {
                $columnName = $modelMapping[$fieldName];
                return "\n\t" . '`' . $columnName . '`=' . self::SQL_CLAUSE_VALUES . '(`' . $columnName . '`)';
            }, $data['fieldNames']));
        }

        return $sql;
    }

    /**
     * Translate where part
     *
     * @param array $data
     * @throws Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function translateWhere(array $data)
    {
        $sql = '';
        $delete = '';

        $deleteClass = array_shift($data);

        if ($deleteClass) {
            $delete = "\n" . self::SQL_STATEMENT_DELETE . ' ' . self::SQL_CLAUSE_FROM .
                "\n\t" . $deleteClass::getTableName();
            $sql .= $delete;
        }

        if (empty($data)) {
            return $sql;
        }

        $sql = '';

        foreach ($data as $modelClass => $items) {
            list($tableAlias, $fieldNames) = $items;

            $fields = $modelClass::getMapping();

            foreach ($fieldNames as $fieldNameArr) {
                list($logicalOperator, $fieldName, $comparsionOperator, $count) = $fieldNameArr;

                $whereQuery = null;

                if (isset($fields[$fieldName])) {
                    $fieldName = $fields[$fieldName];
                }

                switch ($comparsionOperator) {
                    case Query_Builder::SQL_COMPARSION_OPERATOR_EQUAL:
                        $whereQuery = '`' . $fieldName . '` ' . Query_Builder::SQL_COMPARSION_OPERATOR_EQUAL . ' ?';
                        break;
                    case Query_Builder::SQL_COMPARSION_OPERATOR_GREATER:
                        $whereQuery = '`' . $fieldName . '` ' . Query_Builder::SQL_COMPARSION_OPERATOR_GREATER . ' ?';
                        break;
                    case Query_Builder::SQL_COMPARSION_OPERATOR_LESS:
                        $whereQuery = '`' . $fieldName . '` ' . Query_Builder::SQL_COMPARSION_OPERATOR_LESS . ' ?';
                        break;
                    case Query_Builder::SQL_COMPARSION_OPERATOR_GREATER_OR_EQUAL:
                        $whereQuery = '`' . $fieldName . '` ' . Query_Builder::SQL_COMPARSION_OPERATOR_GREATER_OR_EQUAL . ' ?';
                        break;
                    case Query_Builder::SQL_COMPARSION_OPERATOR_LESS_OR_EQUAL:
                        $whereQuery = '`' . $fieldName . '` ' . Query_Builder::SQL_COMPARSION_OPERATOR_LESS_OR_EQUAL . ' ?';
                        break;
                    case Query_Builder::SQL_COMPARSION_OPERATOR_NOT_EQUAL:
                        $whereQuery = $tableAlias . '.' . $fieldName . ' ' . Query_Builder::SQL_COMPARSION_OPERATOR_NOT_EQUAL . ' ?';
                        break;
                    case Query_Builder::SQL_COMPARSION_KEYWORD_IN:
                        $whereQuery = $tableAlias . '.' . $fieldName . ' IN (?' . str_repeat(',?', $count - 1) . ')';
                        break;
                    case Query_Builder::SQL_COMPARSION_KEYWORD_IS_NULL:
                        $whereQuery = $tableAlias . '.' . $fieldName . ' ' . Query_Builder::SQL_COMPARSION_KEYWORD_IS_NULL;
                        break;
                    case Query_Builder::SQL_COMPARSION_KEYWORD_IS_NOT_NULL:
                        $whereQuery = $tableAlias . '.' . $fieldName . ' ' . Query_Builder::SQL_COMPARSION_KEYWORD_IS_NOT_NULL;
                        break;
                    case Query_Builder::SQL_COMPARSION_KEYWORD_LIKE:
                        $whereQuery = $tableAlias . '.' . $fieldName . ' ' . Query_Builder::SQL_COMPARSION_KEYWORD_LIKE . ' ?';
                        break;
                    case Query_Builder::SQL_COMPARSION_KEYWORD_RLIKE:
                        $whereQuery = $tableAlias . '.' . $fieldName . ' ' . Query_Builder::SQL_COMPARSION_KEYWORD_RLIKE . ' ?';
                        break;
                    case Query_Builder::SQL_COMPARSION_KEYWORD_RLIKE_REVERSE:
                        $whereQuery = '? ' . Query_Builder::SQL_COMPARSION_KEYWORD_RLIKE . ' ' . $fieldName;
                        break;
                    default:
                        throw new Exception('Unknown comparsion operator "' . $comparsionOperator . '"');
                }

                $sql .= $sql
                    ? ' ' . $logicalOperator . "\n\t"
                    : "\n" . self::SQL_CLAUSE_WHERE . "\n\t";
                $sql .= $whereQuery;
            }
        }

        return empty($delete) ? $sql : $delete . $sql;
    }

    /**
     * Translate select part
     *
     * @param array $data
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function translateSelect(array $data)
    {
        $sql = '';

        $calcFoundRows = array_shift($data);

        if (empty($data)) {
            return $sql;
        }

        $fields = [];

        foreach ($data as $modelClass => $items) {
            list($tableAlias, $fieldNames) = $items;

            $modelMapping = $modelClass::getMapping();

            foreach ($fieldNames as $fieldName => &$fieldAlias) {
                $isSpatial = (boolean)strpos($fieldName, '__geo');

                if (isset($modelMapping[$fieldName])) {
                    $fieldName = $modelMapping[$fieldName];

                    if ($isSpatial) {
                        $fieldAlias = 'asText(' . $tableAlias . '.' . $fieldName . ')' . ' AS `' . $fieldAlias . '`';
                    } else {
                        $fieldAlias = $fieldAlias == $fieldName
                            ? $fieldName
                            : $tableAlias . '.' . $fieldName . ' AS `' . $fieldAlias . '`';
                    }
                } else {
                    $fieldAlias = $fieldName . ' AS `' . $fieldAlias . '`';
                }
            }

            $fields = array_merge($fields, $fieldNames);
        }

        if (empty($fields)) {
            return $sql;
        }

        reset($data);
        $from = each($data);

        $fromModelClass = $from['key'];

        $sql .= "\n" . self::SQL_STATEMENT_SELECT . ($calcFoundRows ? ' ' . self::SQL_CALC_FOUND_ROWS . ' ' : '') .
            "\n\t" . implode(',' . "\n\t", $fields) .
            "\n" . self::SQL_CLAUSE_FROM .
            "\n\t" . $fromModelClass::getTableName() . ' `' . reset($from['value']) . '`';

        return $sql;
    }

    /**
     * Translate join part
     *
     * @param array $data
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function translateJoin(array $data)
    {
        $sql = '';

        if (empty($data)) {
            return $sql;
        }


        foreach ($data as $joinTable) {
            /** @var Model $joinModelClass */
            $joinModelClass = $joinTable['class'];

            $sql .= "\n" . $joinTable['type'] . "\n\t" .
                $joinModelClass::getTableName() . ' AS `' . $joinTable['alias'] .
                '` ON (' . $joinTable['on'] . ')';
        }

        return $sql;
    }

    /**
     * Translate order part
     *
     * @param array $data
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function translateOrder(array $data)
    {
        $sql = '';

        if (empty($data)) {
            return $sql;
        }

        $orders = [];

        foreach ($data as $modelClass => $item) {
            list($tableAlias, $fieldNames) = $item;

            $fields = $modelClass::getMapping();

            foreach ($fieldNames as $fieldName => $ascending) {
                $orders[] = $tableAlias . '.' . $fields[$fieldName] . ' ' . $ascending;
            }
        }

        $sql .= "\n" . 'ORDER BY ' .
            "\n\t" . implode(',' . "\n\t", $orders);

        return $sql;
    }

    /**
     * Translate group part
     *
     * @param array $data
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function translateGroup(array $data)
    {
        $sql = '';

        if (empty($data)) {
            return $sql;
        }

        $groups = [];

        foreach ($data as $modelClass => $items) {
            list(, $fieldNames) = $items;

            $fields = $modelClass::getMapping();

            foreach ($fieldNames as $fieldName) {
                $groups[] = $fields[$fieldName];
            }
        }

        $sql .= "\n" . 'GROUP BY ' .
            "\n\t" . implode(',' . "\n\t", $groups);

        return $sql;
    }

    /**
     * Translate limit part
     *
     * @param array $data
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function translateLimit($data)
    {
        if (empty($data)) {
            return '';
        }

        list($data, $offset) = $data;

        return "\n" . 'LIMIT ' .
        "\n\t" . $offset . ', ' . $data;
    }
}