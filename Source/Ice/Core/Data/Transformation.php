<?php
/**
 * Ice core data transformation abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

/**
 * Class Data_Transformation
 *
 * Core data transformation abstract class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
abstract class Data_Transformation extends Container
{

    /**
     * Return inctance of Transform class
     *
     * @param $transformationName
     * @throws Exception
     * @return Data_Transformation
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getInstance($transformationName)
    {
        Data_Transformation::getLogger()->fatal('Need implements', __FILE__, __LINE__);
        return null;
    }

    /**
     * Apply transformation for data rows
     *
     * @param $modelClass
     * @param array $rows
     * @param $params
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    abstract function transform($modelClass, array $rows, $params);
} 