<?php
/**
 * Ice data source implementation defined class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Source;

use Ice\Core\Data_Source;
use Ice\Core\Exception;
use Ice\Core\Model;
use Ice\Core\Query;
use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Helper\Query as Helper_Query;

/**
 * Class Defined
 *
 * Implemets defined data source
 *
 * @see Ice\Core\Data_Source
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Data_Source
 *
 * @version 0.0
 * @since 0.0
 */
class Defined extends Data_Source
{
    /**
     * Execute query select to data source
     *
     * @param Query $query
     * @throws Exception
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function select(Query $query)
    {
        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();
        $rows = $this->getConnection($modelClass);

        $pkName = $modelClass::getFieldName('/pk');

        $fieldNames = $modelClass::getMapping();
        $flippedFieldNames = array_flip($fieldNames);

        $definedRows = [];
        foreach ($rows as $pk => &$row) {
            $definedRow = [];
            foreach ($row as $fieldName => $fieldValue) {
                if (isset($flippedFieldNames[$fieldName])) { // Пока такой костыль.. надо думать //dp
                    $definedRow[$flippedFieldNames[$fieldName]] = $fieldValue;
                } else {
                    $definedRow[$fieldName] = $fieldValue;
                }
            }
            $definedRow[$fieldNames[$pkName]] = $pk;
            $definedRows[] = $definedRow;
        }
        $rows = &$definedRows;

        $filterFunction = function ($where) {
            return function ($row) use ($where) {
                foreach ($where as $part) {
                    $whereQuery = null;

                    switch ($part[2]) {
                        case Query_Builder::SQL_COMPARSION_OPERATOR_EQUAL:
                            if (!isset($row[$part[1]]) || $row[$part[1]] != reset($part[3])) {
                                return false;
                            }
                            break;
                        case Query_Builder::SQL_COMPARSION_OPERATOR_NOT_EQUAL:
                            if ($row[$part[1]] == reset($part[3])) {
                                return false;
                            }
                            break;
                        case Query_Builder::SQL_COMPARSION_KEYWORD_IN:
                            if (!in_array($row[$part[1]], $part[3])) {
                                return false;
                            }
                            break;
                        case Query_Builder::SQL_COMPARSION_KEYWORD_IS_NULL:
                            if ($row[$part[1]] !== null) {
                                return false;
                            }
                            break;
                        case Query_Builder::SQL_COMPARSION_KEYWORD_IS_NOT_NULL:
                            if ($row[$part[1]] === null) {
                                return false;
                            }
                            break;
                        default:
                            throw new Exception('Unknown comparsion operator');
                    }
                }

                return true;
            };
        };

        $rows = array_filter($rows, $filterFunction(Helper_Query::convertWhereForFilter($query)));

        return [
            Query_Result::RESULT_MODEL_CLASS => $modelClass,
            Query_Result::RESULT_ROWS => $rows,
            Query_Result::QUERY_FULL_HASH => 'definedHash:' . $query->getSqlPartsHash(),
            Query_Result::NUM_ROWS => count($rows)
        ];
    }

    /**
     * Execute query insert to data source
     *
     * @param Query $query
     * @throws Exception
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function insert(Query $query)
    {
        throw new Exception('Implement insert() method.');
    }

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @throws Exception
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function update(Query $query)
    {
        throw new Exception('Implement update() method.');
    }

    /**
     * Execute query update to data source
     *
     * @param Query $query
     * @throws Exception
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function delete(Query $query)
    {
        throw new Exception('Implement delete() method.');
    }

    /**
     * Return data scheme
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getDataScheme()
    {
        return Data_Source::getDefault()->getDataScheme();
    }

    /**
     * Get data Scheme from data source
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getTables()
    {
        // TODO: Implement getTables() method.
    }

    /**
     * Get table scheme from source
     *
     * @param $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getColumns($tableName)
    {
        // TODO: Implement getColumns() method.
    }
}