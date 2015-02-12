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
 * @version 0.0
 * @since 0.0
 */
class Date
{
    /**
     * 2001-03-10 17:16:18 (формат MySQL DATETIME)
     */
    const FORMAT_MYSQL = 'Y-m-d H:i:s';
    const FORMAT_REVISION = 'mdHi';

    /**
     * Return current data in default (mysql) format
     *
     * @param null $time
     * @param string $format
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function get($time = null, $format = Date::FORMAT_MYSQL)
    {
        return $time ? date($format, $time) : date($format);
    }

    /**
     * Return revision by current time
     *
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function getRevision() {
        return Date::get(null, Date::FORMAT_REVISION);
    }
} 