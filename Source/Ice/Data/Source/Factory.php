<?php
/**
 * Ice data source implementation factory class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Data\Source;

/**
 * Class Factory
 *
 * Implemets factory data source
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
class Factory extends Defined
{
//    /**
//     * Execute query select to data source
//     *
//     * @param Query $query
//     * @throws Exception
//     * @return array
//     */
//    public function select(Query $query)
//    {
//        $data = parent::select($query);
//
//        /** @var Model $modelClass */
//        $modelClass = $data[DATA::RESULT_MODEL_CLASS];
//
//        $modelDelegateClass = $modelClass . '_' . reset($data[DATA::RESULT_ROWS])[$modelClass::getFieldName(
//                '::delegate_name'
//            )];
//
//        $data[DATA::RESULT_MODEL_CLASS] = $modelDelegateClass;
//
//        return $data;
//    }
}