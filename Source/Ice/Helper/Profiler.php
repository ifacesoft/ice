<?php
/**
 * Ice helper memory class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

/**
 * Class Memory
 *
 * Helper memory usage
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 */
class Profiler
{
    /**
     * Return memory size of variable
     *
     * @param mixed $var Variable
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getVarSize($var)
    {
        $start_memory = memory_get_usage();
        $tmp = Json::decode(Json::encode($var));
        return memory_get_usage() - $start_memory;
    }

    /**
     * Get pretty time in milliseconds
     *
     * @param $time
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.6
     */
    public static function getPrettyTime($time)
    {
        return round($time, 5) * 1000 . ' ms';
    }

    /**
     * Get pretty memory in any units
     *
     * @param $memory
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.6
     */
    public static function getPrettyMemory($memory)
    {
        static $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        return round($memory / pow(1024, ($i = floor(log($memory, 1024)))), 2) . ' ' . $unit[$i];
    }
} 