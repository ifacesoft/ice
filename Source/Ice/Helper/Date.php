<?php
/**
 * Ice helper date class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

/**
 * Class Date
 *
 * Helper for dates
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version stable_0
 * @since stable_0
 */
class Date
{
    /**
     * 2001-03-10 17:16:18 (формат MySQL DATETIME)
     */
    const FORMAT = 'Y-m-d H:i:s';

    /**
     * Return current data in default (mysql) format
     *
     * @param null $time
     * @return bool|string
     */
    public static function get($time = null)
    {
        return $time ? date(Date::FORMAT, $time) : date(Date::FORMAT);
    }
} 