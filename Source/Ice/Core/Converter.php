<?php
/**
 * Ice core converter abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;

/**
 * Class Converter
 *
 * Core data transformation abstract class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class Converter extends Container
{
    use Core;

    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    protected static function create($key)
    {
        $converterClass = self::getClass();

        return new $converterClass();
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    /**
     * Apply convert
     *
     * @param  $data
     * @param  $params
     * @return mixed
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public abstract function convert($data, $params);
}
