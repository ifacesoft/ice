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
    use Stored;

    /**
     * @param null $key
     * @param null $ttl
     * @param array $params
     * @return Converter
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   1.0
     */
    public static function getInstance($key = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
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
    abstract public function convert($data, $params);
}
