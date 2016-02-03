<?php
/**
 * Ice query translator implementation sql class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Query\Translator;

use Ice\Core\Exception;
use Ice\Core\Model;
use Ice\Core\Query;
use Ice\Core\Query_Builder;
use Ice\Core\Query_Translator;
use Ice\Helper\Mapping;

/**
 * Class Sql
 *
 * Translate with query translator mysqli
 *
 * @see Ice\Core\Query_Translator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Query_Translator
 */
class Sql extends Query_Translator
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
    const SQL_CLAUSE_HAVING = 'HAVING';
    const SQL_CLAUSE_ORDER = 'ORDER';
    const SQL_CLAUSE_LIMIT = 'LIMIT';
    const ON_DUPLICATE_KEY_UPDATE = 'ON DUPLICATE KEY UPDATE';
    const DEFAULT_KEY = 'instance';

    /**
     * Return default key
     *
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected static function getDefaultKey()
    {
        return Sql::DEFAULT_KEY;
    }

    public function translateDrop(array $part)
    {
        $modelClass = array_shift($part);

        if (empty($modelClass)) {
            return '';
        }

        return "\n" . self::SQL_STATEMENT_DROP . ' `' . $modelClass::getSchemeName() . '`.`' . $modelClass::getTableName() . '`';
    }

    /**
     * Translate set part
     *
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.0
     */
    protected function translateSet(array $part)
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = $part['modelClass'];

        if ($part['rowCount'] > 1) {
            $part['_update'] = true;
            return $this->translateValues($part);
        }

        // @todo tableAlias должен приходить из $part
        $tableAlias = $modelClass::getClassName();

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

        $sql = "\n" . self::SQL_STATEMENT_UPDATE .
            "\n\t`" . $modelClass::getSchemeName() . '`.`' . $modelClass::getTableName() . '` `' . $tableAlias . '`';
        $sql .= "\n" . self::SQL_CLAUSE_SET;
        $sql .= implode(
            ',',
            array_map(
                function ($fieldName) use ($fieldColumnMap, $tableAlias) {
                    $columnName = $fieldColumnMap[$fieldName];
                    return "\n\t`" . $tableAlias . '`.`' . $columnName . '` = ?';
                },
                $part['fieldNames']
            )
        );

        return $sql;
    }

    /**
     * Translate values part
     *
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.0
     */
    protected function translateValues(array $part)
    {
        $update = $part['_update'];
        unset($part['_update']);

        if (empty($part)) {
            return '';
        }

        /**
         * @var Model $modelClass
         */
        $modelClass = $part['modelClass'];

        $sql = "\n" . self::SQL_STATEMENT_INSERT . ' ' . self::SQL_CLAUSE_INTO .
            "\n\t`" . $modelClass::getSchemeName() . '`.`' . $modelClass::getTableName() . '`';

        $fieldNamesCount = count($part['fieldNames']);

        /**
         * Insert empty row
         */
        if (!$fieldNamesCount) {
            $sql .= "\n\t" . '()';
            $sql .= "\n" . self::SQL_CLAUSE_VALUES;
            $sql .= "\n\t" . '()';

            return $sql;
        }

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

        $sql .= "\n\t" . '(`' . implode('`,`', Mapping::columnNames($modelClass, $part['fieldNames'])) . '`)';
        $sql .= "\n" . self::SQL_CLAUSE_VALUES;

        $values = "\n\t" . '(?' . str_repeat(',?', $fieldNamesCount - 1) . ')';

        $sql .= $values;

        if ($part['rowCount'] > 1) {
            $sql .= str_repeat(',' . $values, $part['rowCount'] - 1);
        }

        $fieldNames = array_diff($part['fieldNames'], $modelClass::getScheme()->getPkFieldNames());

        if (empty($fieldNames)) {
            $fieldNames = $part['fieldNames'];
        }

        if ($update) {
            $sql .= "\n" . self::ON_DUPLICATE_KEY_UPDATE;
            $sql .= implode(
                ',',
                array_map(
                    function ($fieldName) use ($fieldColumnMap) {
                        $columnName = $fieldColumnMap[$fieldName];
                        return "\n\t" . '`' . $columnName . '`=' . self::SQL_CLAUSE_VALUES . '(`' . $columnName . '`)';
                    },
                    $fieldNames
                )
            );
        }

        return $sql;
    }

    /**
     * Translate where part
     *
     * @param  array $part
     * @throws Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.3
     * @since   0.0
     */
    protected function translateWhere(array $part)
    {
        $sql = '';
        $delete = '';

        $deleteClass = array_shift($part);

        if ($deleteClass) {

            $tableAlias = reset($part)['alias'];

            $delete = "\n" . self::SQL_STATEMENT_DELETE . ' ' . self::SQL_CLAUSE_FROM .
                "\n\t" . '`' . $tableAlias . '` USING `' . $deleteClass::getSchemeName() . '`.`' . $deleteClass::getTableName() . '` AS `' . $tableAlias . '`';
            $sql .= $delete;
        }

        if (empty($part)) {
            return $sql;
        }

        $sql = '';

        /**
         * @var Model $modelClass
         * @var array $where
         */
        foreach ($part as $where) {
            $modelClass = $where['class'];
            $tableAlias = $where['alias'];

            $fields = $modelClass::getScheme()->getFieldColumnMap();

            foreach ($where['data'] as $fieldNameArr) {
                list($logicalOperator, $fieldName, $comparisonOperator, $count) = $fieldNameArr;

                if (isset($fields[$fieldName])) {
                    $fieldName = $fields[$fieldName];
                }

                $sql .= $sql
                    ? ' ' . $logicalOperator . "\n\t"
                    : "\n" . self::SQL_CLAUSE_WHERE . "\n\t";

                $sql .= '`' . $tableAlias . '`.`' . $fieldName . '` ' .
                    $this->buildWhere($comparisonOperator, $fieldName, $count);
            }
        }

        return empty($delete) ? $sql : $delete . $sql;
    }

    protected function translateHaving(array $part)
    {
        $sql = '';

        /**
         * @var Model $modelClass
         * @var array $having
         */
        foreach ($part as $tableAlias => $having) {
            $modelClass = $having['class'];

            $fields = $modelClass::getScheme()->getFieldColumnMap();

            foreach ($having['data'] as $fieldNameArr) {
                list($logicalOperator, $fieldName, $comparisonOperator, $count) = $fieldNameArr;

                if (isset($fields[$fieldName])) {
                    $fieldName = $fields[$fieldName];
                }

                $sql .= $sql
                    ? ' ' . $logicalOperator . "\n\t"
                    : "\n" . self::SQL_CLAUSE_HAVING . "\n\t";

                $sql .= '`' . $fieldName . '` ' .
                    $this->buildWhere($comparisonOperator, $fieldName, $count);
            }
        }

        return $sql;
    }

    /**
     * Build where part string
     *
     * @param $comparisonOperator
     * @param $fieldName
     * @param $count
     * @return string
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.3
     * @since   0.3
     */
    private function buildWhere($comparisonOperator, $fieldName, $count)
    {
        switch ($comparisonOperator) {
            case Query_Builder::SQL_COMPARISON_OPERATOR_EQUAL:
            case Query_Builder::SQL_COMPARISON_OPERATOR_GREATER:
            case Query_Builder::SQL_COMPARISON_OPERATOR_LESS:
            case Query_Builder::SQL_COMPARISON_OPERATOR_GREATER_OR_EQUAL:
            case Query_Builder::SQL_COMPARISON_OPERATOR_LESS_OR_EQUAL:
            case Query_Builder::SQL_COMPARISON_KEYWORD_REGEXP:
            case Query_Builder::SQL_COMPARISON_OPERATOR_NOT_EQUAL:
            case Query_Builder::SQL_COMPARISON_KEYWORD_LIKE:
            case Query_Builder::SQL_COMPARISON_KEYWORD_RLIKE:
                return $comparisonOperator . ' ?';
            case Query_Builder::SQL_COMPARISON_KEYWORD_BETWEEN:
                return $comparisonOperator . ' ? AND ?';
            case Query_Builder::SQL_COMPARISON_KEYWORD_IN:
            case Query_Builder::SQL_COMPARISON_KEYWORD_NOT_IN:
                return $comparisonOperator . ' (?' . ($count > 1 ? str_repeat(',?', $count - 1) : '') . ')';
            case Query_Builder::SQL_COMPARISON_KEYWORD_IS_NULL:
            case Query_Builder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL:
                return $comparisonOperator;
            case Query_Builder::SQL_COMPARISON_KEYWORD_RLIKE_REVERSE:
                return '? ' . Query_Builder::SQL_COMPARISON_KEYWORD_RLIKE . ' ' . $fieldName;
            default:
                $this->getLogger()->exception(['Unknown comparison operator "{$0}"', $comparisonOperator], __FILE__, __LINE__);
        }

        return '';
    }

    /**
     * Translate select part
     *
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function translateSelect(array $part)
    {
        $sql = '';

        $calcFoundRows = array_shift($part);

        if (empty($part)) {
            return $sql;
        }

        $fields = [];

        /**
         * @var Model $modelClass
         */
        $modelClass = null;
        $tableAlias = null;

        foreach ($part as $modelClass => $tableAliases) {
            foreach ($tableAliases as $tableAlias => $select) {
                $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

                foreach ($select['columns'] as $fieldName => $fieldAlias) {
                    $isSpatial = (boolean)strpos($fieldName, '__geo');

                    if (isset($fieldColumnMap[$fieldName])) {
                        $fieldName = $fieldColumnMap[$fieldName];

                        if ($isSpatial) {
                            $fieldAlias = 'asText(`' . $tableAlias . '`.`' . $fieldName . '`)' . ' AS `' . $fieldAlias . '`';
                        } else {
                            $fieldAlias = $fieldAlias == $fieldName
                                ? '`' . $tableAlias . '`.`' . $fieldName . '`'
                                : (
                                $tableAlias === ''
                                    ? $fieldName . ' AS `' . $fieldAlias . '`'
                                    : '`' . $tableAlias . '`.`' . $fieldName . '` AS `' . $fieldAlias . '`'
                                );
                        }
                    } else {
                        $fieldAlias = $tableAlias === ''
                            ? $fieldName . ' AS `' . $fieldAlias . '`'
                            : '`' . $tableAlias . '`.`' . trim($fieldName, '`') . '` AS `' . trim($fieldAlias, '`') . '`';
                    }

                    $fields[] = $fieldAlias;
                }
            }
        }

        if (empty($fields)) {
            return $sql;
        }

        if (isset($select['table']) && $select['table'] instanceof Query_Builder) {
            $select['table']->setCalcFoundRows(false);
            $select['table'] = $select['table']->getSelectQuery('*'); // todo Не доджно быть никаких Query, только Query_Builder
        }

        $table = isset($select['table']) && $select['table'] instanceof Query
            ? '(' . $select['table']->getBody() . ')'
            : '`' . $modelClass::getSchemeName() . '`.`' . $modelClass::getTableName() . '`';

        $sql .= "\n" . self::SQL_STATEMENT_SELECT . ($calcFoundRows ? ' ' . self::SQL_CALC_FOUND_ROWS . ' ' : '') .
            "\n\t" . implode(',' . "\n\t", $fields) .
            "\n" . self::SQL_CLAUSE_FROM .
            "\n\t" . $table . ' `' . $tableAlias . '`';

        return $sql;
    }

    /**
     * Translate join part
     *
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function translateJoin(array $part)
    {
        $sql = '';

        if (empty($part)) {
            return $sql;
        }

        foreach ($part as $tableAlias => $joinTable) {
            /**
             * @var Model $joinModelClass
             */
            $joinModelClass = $joinTable['class'];

            $sql .= "\n" . $joinTable['type'] . "\n\t`" .
                $joinModelClass::getSchemeName() . '`.`' . $joinModelClass::getTableName() . '` `' . $tableAlias . '` ON (' . $joinTable['on'] . ')';
        }

        return $sql;
    }

    /**
     * Translate order part
     *
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function translateOrder(array $part)
    {
        $sql = '';

        if (empty($part)) {
            return $sql;
        }

        $orders = [];

        /**
         * @var Model $modelClass
         * @var array $item
         */
        foreach ($part as $modelClass => $item) {
            list($tableAlias, $fieldNames) = $item;

            $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

            foreach ($fieldNames as $fieldName => $ascending) {
                $orders[] = isset($fieldColumnMap[$fieldName])
                    ? '`' . $tableAlias . '`.`' . $fieldColumnMap[$fieldName] . '` ' . $ascending
                    : '`' . trim($fieldName, '`') . '` ' . $ascending;
            }
        }

        $sql .= "\n" . 'ORDER BY ' .
            "\n\t" . implode(',' . "\n\t", $orders);

        return $sql;
    }

    /**
     * Translate group part
     *
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function translateGroup(array $part)
    {
        $sql = '';

        if (empty($part)) {
            return $sql;
        }

        $groups = [];

        /**
         * @var Model $modelClass
         * @var array $items
         */
        foreach ($part as $modelClass => $items) {
            list($tableAlias, $fieldNames) = $items;

            $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

            foreach ($fieldNames as $fieldName) {
                $groups[] = isset($fieldColumnMap[$fieldName])
                    ? '`' . $tableAlias . '`.`' . $fieldColumnMap[$fieldName] . '`'
                    : $fieldName; // custom field (check yourself)
            }
        }

        $sql .= "\n" . 'GROUP BY ' .
            "\n\t" . implode(',' . "\n\t", $groups);

        return $sql;
    }

    /**
     * Translate limit part
     *
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function translateLimit($part)
    {
        if (empty($part) || empty($part['limit'])) {
            return '';
        }

        return "\n" . 'LIMIT ' .
        "\n\t" . $part['offset'] . ', ' . $part['limit'];
    }

    /**
     * Translate create part
     *
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     */
    protected function translateCreate(array $part)
    {
        $sql = '';

        if (empty($part)) {
            return $sql;
        }

        $scheme = each($part);

        /**
         * @var Model $modelClass
         */
        $modelClass = $scheme['key'];

        array_walk(
            $scheme['value'],
            function (&$scheme, $columnName) {
                $type = strtoupper($scheme['type']);

                if (empty($scheme['default']) || $type == 'TEXT') {
                    $default = '';
                } else {
                    switch (strtoupper($scheme['dataType'])) {
                        case 'VARCHAR':
                            $default = '\'' . $scheme['default'] . '\'';
                            break;

                        default:
                            $default = $scheme['default'];
                    }

                    $default = 'DEFAULT ' . $default . ' ';
                }

                $scheme = $columnName . ' ' .
                    $type . ' ' .
                    (empty($scheme['extra']) ? '' : strtoupper($scheme['extra']) . ' ') .
                    ($scheme['extra'] ? 'PRIMARY KEY ' : '') .
                    $default .
                    ($scheme['nullable'] ? 'NULL' : 'NOT NULL');
            }
        );

        $sql .= "\n" . self::SQL_STATEMENT_CREATE . ' `' . $modelClass::getSchemeName() . '`.`' . $modelClass::getTableName() . '`' .
            "\n" . '(' .
            "\n\t" . implode(',' . "\n\t", $scheme['value']) .
            "\n" . ') ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';

        return $sql;
    }
}
