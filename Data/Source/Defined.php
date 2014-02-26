<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 30.12.13
 * Time: 23:52
 */

namespace ice\data\source;

use ice\core\Data;
use ice\core\Data_Source;
use ice\core\Model;
use ice\core\Query;
use ice\Exception;

class Defined extends Data_Source
{
    /**
     * @param Query $query
     * @return array
     */
    public function select(Query $query)
    {
        $rows = $this->getConnection();

        /** @var Model $modelClass */
        $modelClass = $query->getModelClass();

        $pkName = $modelClass::getPkName();

        foreach ($rows as $pk => &$row) {
            $row[$pkName] = $pk;
        }

        $filterFunction = function ($where) {
            return function ($row) use ($where) {
                foreach ($where as list($part, $bind)) {
                    $whereQuery = null;

                    switch ($part[Query::CLAUSE_WHERE_COMPARSION_OPERATOR]) {
                        case Query::SQL_COMPARSION_OPERATOR_EQUAL:
                            if ($row[$part[Query::CLAUSE_WHERE_FIELD_NAME]] != $bind) {
                                return false;
                            }
                            break;
                        case Query::SQL_COMPARSION_OPERATOR_NOT_EQUAL:
                            if ($row[$part[Query::CLAUSE_WHERE_FIELD_NAME]] == $bind) {
                                return false;
                            }
                            break;
                        case Query::SQL_COMPARSION_KEYWORD_IN:
                            if (!in_array($row[$part[Query::CLAUSE_WHERE_FIELD_NAME]], $bind)) {
                                return false;
                            }
                            break;
                        case Query::SQL_COMPARSION_KEYWORD_IS_NULL:
                            if ($row[$part[Query::CLAUSE_WHERE_FIELD_NAME]] !== null) {
                                return false;
                            }
                            break;
                        case Query::SQL_COMPARSION_KEYWORD_IS_NOT_NULL:
                            if ($row[$part[Query::CLAUSE_WHERE_FIELD_NAME]] === null) {
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

        $rows = array_filter($rows, $filterFunction($query->getWhere()));

        return array(
            Data::RESULT_MODEL_CLASS => $modelClass,
            Data::RESULT_ROWS => $rows,
            Data::RESULT_SQL => 'todo: need implements',
            Data::NUM_ROWS => count($rows)
        );
    }

    /**
     * @param Query $query
     * @throws Exception
     * @return array
     */
    public function insert(Query $query)
    {
        throw new Exception('Implement insert() method.');
    }

    /**
     * @param Query $query
     * @throws Exception
     * @return array
     */
    public function update(Query $query)
    {
        throw new Exception('Implement update() method.');
    }

    /**
     * @param Query $query
     * @throws Exception
     * @return array
     */
    public function delete(Query $query)
    {
        throw new Exception('Implement delete() method.');
    }
}