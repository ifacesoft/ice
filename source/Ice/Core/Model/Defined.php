<?php
/**
 * Ice core model defined abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

/**
 * Class Model_Defined
 *
 * Core defined abstract model class
 *
 * @see Ice\Core\Model
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since   0.0
 */
abstract class Model_Defined extends Model
{
    /**
     * Return defined data (config with rows)
     *
     * @return Config
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getDefinedConfig()
    {
        return Config::getInstance(self::getClass(), 'Defined', true);
    }
}
