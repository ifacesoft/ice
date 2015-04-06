<?php
/**
 * Ice query translator implementation mongodb class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Query\Translator;

use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Query_Builder;
use Ice\Core\Query_Translator;
use Ice\Helper\Mapping;

/**
 * Class Mongodb
 *
 * Translate with query translator mysqli
 *
 * @see Ice\Core\Query_Translator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Query_Translator
 */
class Mongodb extends Query_Translator
{
    const DEFAULT_CLASS_KEY = 'Ice:Mongodb/default';
    const DEFAULT_KEY = 'instance';

    private static $_operators = [
        Query_Builder::SQL_COMPARISON_OPERATOR_GREATER => '$gt',
        Query_Builder::SQL_COMPARISON_OPERATOR_LESS => '$lt',
        Query_Builder::SQL_COMPARISON_OPERATOR_GREATER_OR_EQUAL => '$gte',
        Query_Builder::SQL_COMPARISON_OPERATOR_LESS_OR_EQUAL => '$lte',
        Query_Builder::SQL_COMPARISON_KEYWORD_REGEXP => '$regex',
        Query_Builder::SQL_COMPARISON_OPERATOR_NOT_EQUAL => '$ne',
        Query_Builder::SQL_COMPARISON_KEYWORD_IN => '$in',
        Query_Builder::SQL_COMPARISON_KEYWORD_IS_NULL => '$notExists', // dummy
        Query_Builder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL => '$exists',
        Query_Builder::SQL_COMPARISON_KEYWORD_LIKE => '$like', // dummy
        Query_Builder::SQL_COMPARISON_KEYWORD_RLIKE => '$rlike', // dummy
        Query_Builder::SQL_COMPARISON_KEYWORD_RLIKE_REVERSE => '$rlikeReverse', // dummy
        Query_Builder::SEARCH_KEYWORD => '$search'
    ];

    private static $_orderings = [
        Query_Builder::SQL_ORDERING_ASC => 1,
        Query_Builder::SQL_ORDERING_DESC => -1
    ];

    /**
     * Translate set part
     *
     * @param array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected function translateSet(array $part)
    {
        /** @var Model $modelClass */
        $modelClass = $part['modelClass'];

        if ($part['rowCount'] > 1) {
            $part['_update'] = true;
            return $this->translateValues($part);
        }

        $columnNames = [];

        foreach (Mapping::columnNames($modelClass, $part['fieldNames']) as $columnName) {
            $columnNames['$set'] = $columnName;
        }

        return [
            'update' => [
                'modelClass' => $modelClass,
                'columnNames' => $columnNames,
                'rowCount' => $part['rowCount']
            ]
        ];

//        $sql = "\n" . self::SQL_STATEMENT_UPDATE .
//            "\n\t" . $modelClass::getTableName();
//        $sql .= "\n" . self::SQL_CLAUSE_SET;
//        $sql .= "\n\t" . '`' . implode('`=?,`', array_map(function ($fieldName) use ($modelMapping) {
//                return $modelMapping[$fieldName];
//            }, $part['fieldNames'])) . '`=?';
//
//        return $sql;
    }

    /**
     * Translate values part
     *
     * @param array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected function translateValues(array $part)
    {
        $update = $part['_update'];
        unset($part['_update']);

        if (empty($part)) {
            return [];
        }

        /** @var Model $modelClass */
        $modelClass = $part['modelClass'];

        $columnNames = [];

        foreach (Mapping::columnNames($modelClass, $part['fieldNames']) as $columnName) {
            $columnNames[$columnName][] = null;
        }

        return [
            'insert' => [
                'modelClass' => $modelClass,
                'columnNames' => $columnNames,
                'rowCount' => $part['rowCount']
            ]
        ];

//        $sql = "\n" . self::SQL_STATEMENT_INSERT . ' ' . self::SQL_CLAUSE_INTO .
//            "\n\t" . $modelClass::getTableName();
//
//        $fieldNamesCount = count($part['fieldNames']);
//
//        /** Insert empty row */
//        if (!$fieldNamesCount) {
//            $sql .= "\n\t" . '()';
//            $sql .= "\n" . self::SQL_CLAUSE_VALUES;
//            $sql .= "\n\t" . '()';
//
//            return $sql;
//        }
//
//        $modelMapping = $modelClass::getScheme()->getFieldMapping();
//
//        $sql .= "\n\t" . '(`' . implode('`,`', Mapping::columnNames($modelClass, $part['fieldNames'])) . '`)';
//        $sql .= "\n" . self::SQL_CLAUSE_VALUES;
//
//        $values = "\n\t" . '(?' . str_repeat(',?', $fieldNamesCount - 1) . ')';
//
//        $sql .= $values;
//
//        if ($part['rowCount'] > 1) {
//            $sql .= str_repeat(',' . $values, $part['rowCount'] - 1);
//        }
//
//        if ($update) {
//            $sql .= "\n" . self::ON_DUPLICATE_KEY_UPDATE;
//            $sql .= implode(',', array_map(function ($fieldName) use ($modelMapping) {
//                $columnName = $modelMapping[$fieldName];
//                return "\n\t" . '`' . $columnName . '`=' . self::SQL_CLAUSE_VALUES . '(`' . $columnName . '`)';
//            }, $part['fieldNames']));
//        }
//
//        return $sql;
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
     * @version 0.4
     * @since 0.4
     */
    protected function translateWhere(array $part)
    {
        $sql = [];
        $delete = '';

        $deleteClass = array_shift($part);

//        if ($deleteClass) {
//            $tableAlias = reset($part)[0];
//
//            $delete = "\n" . self::SQL_STATEMENT_DELETE . ' ' . self::SQL_CLAUSE_FROM .
//                "\n\t" . '`' . $tableAlias . '` USING ' . $deleteClass::getTableName() . ' AS  `' . $tableAlias . '`';
//            $sql .= $delete;
//        }

        if (empty($part)) {
            return $sql;
        }

        /** @var Model $modelClass */
        list($modelClass, $items) = each($part);

        list($tableAlias, $fieldNames) = $items;

        $columnNames = [];

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

        foreach ($fieldNames as $fieldNameArr) {
            list($logicalOperator, $fieldName, $comparisonOperator, $count) = $fieldNameArr;

            for ($i = 0; $i < $count; $i++) {
                $columnName = isset($fieldColumnMap[$fieldName])
                    ? $fieldColumnMap[$fieldName]
                    : $fieldName;

                $columnNames[$columnName][] = $comparisonOperator == Query_Builder::SQL_COMPARISON_OPERATOR_EQUAL
                    ? null
                    : Mongodb::$_operators[$comparisonOperator];
            }
        }

        return [
            'where' => [
                'modelClass' => $modelClass,
                'columnNames' => $columnNames,
            ]
        ];

//        $sql = [];
//
//        foreach ($part as $modelClass => $items) {
//            list($tableAlias, $fieldNames) = $items;
//
//            foreach ($fieldNames as $fieldNameArr) {
//                list($logicalOperator, $fieldName, $comparisonOperator, $count) = $fieldNameArr;
//
//                $sql .= $sql
//                    ? ' ' . $logicalOperator . "\n\t"
//                    : "\n" . self::SQL_CLAUSE_WHERE . "\n\t";
//                $sql .= $this->buildWhere($modelClass::getScheme()->getFieldMapping(), $fieldName, $comparisonOperator, $tableAlias, $count);
//            }
//        }
//
//        return empty($delete) ? $sql : $delete . $sql;
    }

    /**
     * Translate select part
     *
     * @param array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected function translateSelect(array $part)
    {
        $sql = [];

        $calcFoundRows = array_shift($part);

        if (empty($part)) {
            return $sql;
        }

        list($modelClass, $items) = each($part);

        list($tableAlias, $fieldNames) = each($items);

        $columnNames = [];

        foreach (Mapping::columnNames($modelClass, array_keys($fieldNames)) as $columnName) {
            $columnNames[] = $columnName;
        }

        return [
            'select' => [
                'modelClass' => $modelClass,
                'columnNames' => $columnNames,
            ]
        ];

//        $fields = [];
//
//        /** @var Model $modelClass */
//        $modelClass = null;
//        $tableAlias = null;
//
//        foreach ($part as $modelClass => $items) {
//            list($tableAlias, $fieldNames) = $items;
//
//            $modelMapping = $modelClass::getScheme()->getFieldMapping();
//
//            foreach ($fieldNames as $fieldName => &$fieldAlias) {
//                $isSpatial = (boolean)strpos($fieldName, '__geo');
//
//                if (isset($modelMapping[$fieldName])) {
//                    $fieldName = $modelMapping[$fieldName];
//
//                    if ($isSpatial) {
//                        $fieldAlias = 'asText(' . $tableAlias . '.' . $fieldName . ')' . ' AS `' . $fieldAlias . '`';
//                    } else {
//                        $fieldAlias = $fieldAlias == $fieldName
//                            ? $tableAlias . '.' . $fieldName
//                            : $tableAlias . '.' . $fieldName . ' AS `' . $fieldAlias . '`';
//                    }
//                } else {
//                    $fieldAlias = $tableAlias . '.' . $fieldName . ' AS `' . $fieldAlias . '`';
//                }
//            }
//
//            $fields = array_merge($fields, $fieldNames);
//        }
//
//        if (empty($fields)) {
//            return $sql;
//        }
//
//        $sql .= "\n" . self::SQL_STATEMENT_SELECT . ($calcFoundRows ? ' ' . self::SQL_CALC_FOUND_ROWS . ' ' : '') .
//            "\n\t" . implode(',' . "\n\t", $fields) .
//            "\n" . self::SQL_CLAUSE_FROM .
//            "\n\t" . $modelClass::getTableName() . ' `' . $tableAlias . '`';
//
//        return $sql;
    }

    /**
     * Translate join part
     *
     * @param array $part
     * @return string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected function translateJoin(array $part)
    {
        if (empty($part)) {
            return [];
        } else {
            Debuger::dump($part);
            throw new \Exception('Not implemented');
        }


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
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected function translateOrder(array $part)
    {
        if (empty($part)) {
            return [];
        }

        /** @var Model $modelClass */
        list($modelClass, $items) = each($part);

        list($tableAlias, $fieldNames) = $items;

        $columnNames = [];

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

        foreach ($fieldNames as $fieldName => $ordering) {
            $columnNames[$fieldColumnMap[$fieldName]] = self::$_orderings[$ordering];
        }

        return [
            'order' => [
                'modelClass' => $modelClass,
                'columnNames' => $columnNames,
            ]
        ];
    }

    /**
     * Translate group part
     *
     * @param array $part
     * @return string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected function translateGroup(array $part)
    {
        if (empty($part)) {
            return [];
        } else {
            Debuger::dump($part);
            throw new \Exception('Not implemented');
        }


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
            list(, $fieldNames) = $items;

            $fields = $modelClass::getScheme()->getFieldColumnMap();

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
     * @version 0.4
     * @since 0.4
     */
    protected function translateLimit($part)
    {
        if (empty($part)) {
            return [];
        }

        list($limit, $skip) = $part;

        return [
            'limit' => [
                'limit' => $limit,
                'skip' => $skip,
            ]
        ];
    }

    /**
     * Translate create part
     *
     * @param array $part
     * @return string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected function translateCreate(array $part)
    {
        if (empty($part)) {
            return [];
        }

        $scheme = each($part);

        /** @var Model $modelClass */
        $modelClass = $scheme['key'];

        return [
            'create' => [
                'modelClass' => $modelClass,
            ]
        ];
    }

    /**
     * Translate drop table part
     *
     * @param array $part
     * @return array|string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function translateDrop(array $part)
    {
        $modelClass = array_shift($part);

        if (empty($modelClass)) {
            return [];
        }

        return [
            'drop' => [
                'modelClass' => $modelClass,
            ]
        ];
    }

    /**
     * Return default class key
     *
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected static function getDefaultClassKey()
    {
        return Mongodb::DEFAULT_CLASS_KEY;
    }

    /**
     * Return default key
     *
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    protected static function getDefaultKey()
    {
        return Mongodb::DEFAULT_KEY;
    }
}