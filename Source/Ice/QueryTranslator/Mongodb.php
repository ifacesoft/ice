<?php
/**
 * Ice query translator implementation mongodb class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\QueryTranslator;

use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Model;
use Ice\Core\QueryBuilder;
use Ice\Core\QueryTranslator;
use Ice\Helper\Mapping;

/**
 * Class Mongodb
 *
 * Translate with query translator mysqli
 *
 * @see Ice\Core\QueryTranslator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage QueryTranslator
 */
class Mongodb extends QueryTranslator
{
    const DEFAULT_KEY = 'instance';

    private static $operators = [
        QueryBuilder::SQL_COMPARISON_OPERATOR_GREATER => '$gt',
        QueryBuilder::SQL_COMPARISON_OPERATOR_LESS => '$lt',
        QueryBuilder::SQL_COMPARISON_OPERATOR_GREATER_OR_EQUAL => '$gte',
        QueryBuilder::SQL_COMPARISON_OPERATOR_LESS_OR_EQUAL => '$lte',
        QueryBuilder::SQL_COMPARISON_KEYWORD_REGEXP => '$regex',
        QueryBuilder::SQL_COMPARISON_OPERATOR_NOT_EQUAL => '$ne',
        QueryBuilder::SQL_COMPARISON_KEYWORD_IN => '$in',
        QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NULL => '$notExists', // dummy
        QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL => '$exists',
        QueryBuilder::SQL_COMPARISON_KEYWORD_LIKE => '$like', // dummy
        QueryBuilder::SQL_COMPARISON_KEYWORD_RLIKE => '$rlike', // dummy
        QueryBuilder::SQL_COMPARISON_KEYWORD_RLIKE_REVERSE => '$rlikeReverse', // dummy
        QueryBuilder::SEARCH_KEYWORD => '$search'
    ];

    private static $orderings = [
        QueryBuilder::SQL_ORDERING_ASC => 1,
        QueryBuilder::SQL_ORDERING_DESC => -1
    ];

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
        return Mongodb::DEFAULT_KEY;
    }

    /**
     * Translate drop table part
     *
     * @param  array $part
     * @return array|string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
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
     * Translate set part
     *
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
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
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected function translateValues(array $part)
    {
        $update = $part['_update'];
        unset($part['_update']);

        if (empty($part)) {
            return [];
        }

        /**
         * @var Model $modelClass
         */
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
     * @version 0.4
     * @since   0.4
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

        /**
         * @var Model $modelClass
         */
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

                $columnNames[$columnName][] = $comparisonOperator == QueryBuilder::SQL_COMPARISON_OPERATOR_EQUAL
                    ? null
                    : Mongodb::$operators[$comparisonOperator];
            }
        }

        return [
            'where' => [
                'modelClass' => $modelClass,
                'columnNames' => $columnNames,
            ]
        ];
    }

    /**
     * Translate select part
     *
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected function translateSelect(array $part)
    {
        $sql = [];

        $calcFoundRows = array_shift($part);

        $distinct = array_shift($part);

        if (empty($part)) {
            return $sql;
        }

        list($modelClass, $items) = each($part);

        list($tableAlias, $select) = each($items);

        $columnNames = [];

        foreach (Mapping::columnNames($modelClass, array_keys($select['columns'])) as $columnName) {
            $columnNames[] = $columnName;
        }

        return [
            'select' => [
                'modelClass' => $modelClass,
                'columnNames' => $columnNames,
            ]
        ];
    }

    /**
     * Translate join part
     *
     * @param  array $part
     * @return string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
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
            /**
             * @var Model $joinModelClass
             */
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
     * @param  array $part
     * @return string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected function translateOrder(array $part)
    {
        if (empty($part)) {
            return [];
        }

        /**
         * @var Model $modelClass
         */
        list($modelClass, $items) = each($part);

        list($tableAlias, $fieldNames) = $items;

        $columnNames = [];

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();

        foreach ($fieldNames as $fieldName => $ordering) {
            $columnNames[$fieldColumnMap[$fieldName]] = self::$orderings[$ordering];
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
     * @param  array $part
     * @return string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
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
     * @param  array $part
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.4
     */
    protected function translateLimit($part)
    {
        if (empty($part) || empty($part['limit'])) {
            return [];
        }

        return [
            'limit' => [
                'limit' => $part['limit'],
                'skip' => $part['offset'],
            ]
        ];
    }

    /**
     * Translate create part
     *
     * @param  array $part
     * @return string
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    protected function translateCreate(array $part)
    {
        if (empty($part)) {
            return [];
        }

        $scheme = each($part);

        /**
         * @var Model $modelClass
         */
        $modelClass = $scheme['key'];

        return [
            'create' => [
                'modelClass' => $modelClass,
            ]
        ];
    }
}
