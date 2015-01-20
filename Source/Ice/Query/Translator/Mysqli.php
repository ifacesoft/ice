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
    const SQL_STATEMENT_CREATE = 'CREATE TABLE IF NOT EXISTS';
    const SQL_STATEMENT_DROP = 'DROP TABLE IF EXISTS';
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

        foreach ($sqlParts as $sqlPart => $part) {
            if (empty($part)) {
                continue;
            }

            $translate = 'translate' . ucfirst($sqlPart);
            $sql .= $this->$translate($part);
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
     * @param array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    private function translateSet(array $part)
    {
        /** @var Model $modelClass */
        $modelClass = $part['modelClass'];

        $modelMapping = $modelClass::getMapping();

        if ($part['rowCount'] > 1) {
            $part['_update'] = true;
            return $this->translateValues($part);
        }

        $sql = "\n" . self::SQL_STATEMENT_UPDATE .
            "\n\t" . $modelClass::getTableName();
        $sql .= "\n" . self::SQL_CLAUSE_SET;
        $sql .= "\n\t" . '`' . implode('`=?,`', array_map(function ($fieldName) use ($modelMapping) {
                return $modelMapping[$fieldName];
            }, $part['fieldNames'])) . '`=?';

        return $sql;
    }

    /**
     * Translate values part
     *
     * @param array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    private function translateValues(array $part)
    {
        $update = $part['_update'];
        unset($part['_update']);

        if (empty($part)) {
            return '';
        }

        /** @var Model $modelClass */
        $modelClass = $part['modelClass'];

        $sql = "\n" . self::SQL_STATEMENT_INSERT . ' ' . self::SQL_CLAUSE_INTO .
            "\n\t" . $modelClass::getTableName();

        $fieldNamesCount = count($part['fieldNames']);

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
            }, $part['fieldNames'])) . '`)';
        $sql .= "\n" . self::SQL_CLAUSE_VALUES;

        $values = "\n\t" . '(?' . str_repeat(',?', $fieldNamesCount - 1) . ')';

        $sql .= $values;

        if ($part['rowCount'] > 1) {
            $sql .= str_repeat(',' . $values, $part['rowCount'] - 1);
        }

        if ($update) {
            $sql .= "\n" . self::ON_DUPLICATE_KEY_UPDATE;
            $sql .= implode(',', array_map(function ($fieldName) use ($modelMapping) {
                $columnName = $modelMapping[$fieldName];
                return "\n\t" . '`' . $columnName . '`=' . self::SQL_CLAUSE_VALUES . '(`' . $columnName . '`)';
            }, $part['fieldNames']));
        }

        return $sql;
    }

    /**
     * Build where part string
     *
     * @param array $fields
     * @param $fieldName
     * @param $comparisonOperator
     * @param $tableAlias
     * @param $count
     * @return string
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.3
     * @since 0.3
     */
    private function buildWhere(array $fields, $fieldName, $comparisonOperator, $tableAlias, $count)
    {
        if (isset($fields[$fieldName])) {
            $fieldName = $fields[$fieldName];
        }

        switch ($comparisonOperator) {
            case Query_Builder::SQL_COMPARSION_OPERATOR_EQUAL:
                return '`' . $fieldName . '` ' . Query_Builder::SQL_COMPARSION_OPERATOR_EQUAL . ' ?';
            case Query_Builder::SQL_COMPARSION_OPERATOR_GREATER:
                return '`' . $fieldName . '` ' . Query_Builder::SQL_COMPARSION_OPERATOR_GREATER . ' ?';
            case Query_Builder::SQL_COMPARSION_OPERATOR_LESS:
                return '`' . $fieldName . '` ' . Query_Builder::SQL_COMPARSION_OPERATOR_LESS . ' ?';
            case Query_Builder::SQL_COMPARSION_OPERATOR_GREATER_OR_EQUAL:
                return '`' . $fieldName . '` ' . Query_Builder::SQL_COMPARSION_OPERATOR_GREATER_OR_EQUAL . ' ?';
            case Query_Builder::SQL_COMPARSION_OPERATOR_LESS_OR_EQUAL:
                return '`' . $fieldName . '` ' . Query_Builder::SQL_COMPARSION_OPERATOR_LESS_OR_EQUAL . ' ?';
            case Query_Builder::SQL_COMPARSION_OPERATOR_NOT_EQUAL:
                return $tableAlias . '.' . $fieldName . ' ' . Query_Builder::SQL_COMPARSION_OPERATOR_NOT_EQUAL . ' ?';
            case Query_Builder::SQL_COMPARSION_KEYWORD_IN:
                return $tableAlias . '.' . $fieldName . ' IN (?' . str_repeat(',?', $count - 1) . ')';
            case Query_Builder::SQL_COMPARSION_KEYWORD_IS_NULL:
                return $tableAlias . '.' . $fieldName . ' ' . Query_Builder::SQL_COMPARSION_KEYWORD_IS_NULL;
            case Query_Builder::SQL_COMPARSION_KEYWORD_IS_NOT_NULL:
                return $tableAlias . '.' . $fieldName . ' ' . Query_Builder::SQL_COMPARSION_KEYWORD_IS_NOT_NULL;
            case Query_Builder::SQL_COMPARSION_KEYWORD_LIKE:
                return $tableAlias . '.' . $fieldName . ' ' . Query_Builder::SQL_COMPARSION_KEYWORD_LIKE . ' ?';
            case Query_Builder::SQL_COMPARSION_KEYWORD_RLIKE:
                return $tableAlias . '.' . $fieldName . ' ' . Query_Builder::SQL_COMPARSION_KEYWORD_RLIKE . ' ?';
            case Query_Builder::SQL_COMPARSION_KEYWORD_RLIKE_REVERSE:
                return '? ' . Query_Builder::SQL_COMPARSION_KEYWORD_RLIKE . ' ' . $fieldName;
            default:
                throw new Exception('Unknown comparison operator "' . $comparisonOperator . '"');
        }

    }

    /**
     * Translate where part
     *
     * @param array $part
     * @throws Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.3
     * @since 0.0
     */
    private function translateWhere(array $part)
    {
        $sql = '';
        $delete = '';

        $deleteClass = array_shift($part);

        if ($deleteClass) {
            $delete = "\n" . self::SQL_STATEMENT_DELETE . ' ' . self::SQL_CLAUSE_FROM .
                "\n\t" . $deleteClass::getTableName();
            $sql .= $delete;
        }

        if (empty($part)) {
            return $sql;
        }

        $sql = '';

        foreach ($part as $modelClass => $items) {
            list($tableAlias, $fieldNames) = $items;

            foreach ($fieldNames as $fieldNameArr) {
                list($logicalOperator, $fieldName, $comparisonOperator, $count) = $fieldNameArr;

                $sql .= $sql
                    ? ' ' . $logicalOperator . "\n\t"
                    : "\n" . self::SQL_CLAUSE_WHERE . "\n\t";
                $sql .= $this->buildWhere($modelClass::getMapping(), $fieldName, $comparisonOperator, $tableAlias, $count);
            }
        }

        return empty($delete) ? $sql : $delete . $sql;
    }

    /**
     * Translate select part
     *
     * @param array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function translateSelect(array $part)
    {
        $sql = '';

        $calcFoundRows = array_shift($part);

        if (empty($part)) {
            return $sql;
        }

        $fields = [];

        /** @var Model $modelClass */
        $modelClass = null;
        $tableAlias = null;

        foreach ($part as $modelClass => $items) {
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

        $sql .= "\n" . self::SQL_STATEMENT_SELECT . ($calcFoundRows ? ' ' . self::SQL_CALC_FOUND_ROWS . ' ' : '') .
            "\n\t" . implode(',' . "\n\t", $fields) .
            "\n" . self::SQL_CLAUSE_FROM .
            "\n\t" . $modelClass::getTableName() . ' `' . $tableAlias . '`';

        return $sql;
    }

    /**
     * Translate join part
     *
     * @param array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function translateJoin(array $part)
    {
        $sql = '';

        if (empty($part)) {
            return $sql;
        }

        foreach ($part as $joinTable) {
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
     * @param array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function translateOrder(array $part)
    {
        $sql = '';

        if (empty($part)) {
            return $sql;
        }

        $orders = [];

        foreach ($part as $modelClass => $item) {
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
     * @param array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function translateGroup(array $part)
    {
        $sql = '';

        if (empty($part)) {
            return $sql;
        }

        $groups = [];

        foreach ($part as $modelClass => $items) {
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
     * @param array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function translateLimit($part)
    {
        if (empty($part)) {
            return '';
        }

        list($part, $offset) = $part;

        return "\n" . 'LIMIT ' .
        "\n\t" . $offset . ', ' . $part;
    }

    /**
     * Translate create part
     *
     * @param array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    private function translateCreate(array $part)
    {
        $sql = '';

        if (empty($part)) {
            return $sql;
        }

        $scheme = each($part);

        /** @var Model $modelClass */
        $modelClass = $scheme['key'];

        array_walk(
            $scheme['value'],
            function (&$scheme, $columnName) {
                $scheme = $columnName . ' ' .
                    strtoupper($scheme['type']) . ' ' .
                    (empty($scheme['extra']) ? '' : strtoupper($scheme['extra']) . ' ') .
                    ($scheme['extra'] ? 'PRIMARY KEY ' : '') .
                    (empty($scheme['default']) ? '' : 'DEFAULT ' . $scheme['default'] . ' ') .
                    (empty($scheme['nullable']) ? 'NULL' : 'NOT NULL');
            }
        );

        $sql .= "\n" . self::SQL_STATEMENT_CREATE . ' `' . $modelClass::getTableName() . '`' .
            "\n" . '(' .
            "\n\t" . implode(',' . "\n\t", $scheme['value']) .
            "\n" . ') ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';

        return $sql;
    }

    public function translateDrop(array $part)
    {
        $modelClass = array_shift($part);

        if (empty($modelClass)) {
            return '';
        }

        return "\n" . self::SQL_STATEMENT_DROP . ' `' . $modelClass::getTableName() . '`';
    }
}