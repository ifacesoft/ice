<?php
/**
 * Ice helper query class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

/**
 * Class Query
 *
 * Helper for queries
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since 0.0
 */
class Query
{
    /**
     * Convert where part to array filter format
     *
     * @param Query $query
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function convertWhereForFilter(Query $query)
    {
        $where = $query->getSqlPart('where');
        $binds = $query->getBindPart('where');

        $filterFields = [];

        array_shift($where);

        foreach ($where as $whereParts) {
            list(, $fields) = $whereParts;
            foreach ($fields as $field) {
                $values = [];
                for ($i = 0; $i < $field[3]; $i++) {
                    $value = array_shift($binds);
                    if ($value === null) {
                        $values = null;
                    } else {
                        $values[] = $value;
                    }
                }
                $field[3] = $values;
                $filterFields[] = $field;
            }
        }

        return $filterFields;
    }

//SELECT T2.id, T2.table_name
//FROM (
//SELECT
//@r AS _id,
//(SELECT @r := table_id FROM ice_table WHERE id = _id) AS parent_id,
//@l := @l + 1 AS lvl
//FROM
//(SELECT @r := 113, @l := 0) vars,
//ice_table h
//WHERE @r <> 0) T1
//JOIN ice_table T2
//ON T1._id = T2.id
//ORDER BY T1.lvl DESC
}