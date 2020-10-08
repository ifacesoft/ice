<?php
/**
 * Ice helper memory class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use DateTime;

/**
 * Class Memory
 *
 * Helper memory usage
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 */
class Profiler
{
    /**
     * Return memory size of variable
     *
     * @param $start_memory
     * @param mixed $var Variable
     * @param bool $toString
     * @return int
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.0
     */
    public static function getVar($start_memory, $var, $toString = false)
    {
        try {
//            ob_start();
//            print_r($var);
//            $varSize = ob_get_length();
//            ob_end_clean();

            $var = unserialize(serialize($var));
            $varSize = memory_get_usage() - $start_memory;

            $maxVarSize = pow(2, 18);

            if ($varSize > $maxVarSize) {
//            Core_Logger::getInstance(__CLASS__)->warning(['Too big data: {$0} bytes (max: {$1})', [Profiler::getPrettyMemory($varSize), Profiler::getPrettyMemory($maxVarSize)]], __FILE__, __LINE__);

                return null;
            }

            return $toString ? Php::varToPhpString($var) : $var;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get pretty time in milliseconds
     *
     * @param  $time
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     * @todo сделать так-же как memory
     * @version 0.6
     * @since   0.6
     */
    public static function getPrettyTime($time)
    {
        $seconds = (int)$time;

        $miliseconds = round(($time - $seconds) * 1000, 0);

        $diff = (new DateTime('@0'))->diff(new DateTime("@$seconds"));

        $date = '';

        if ($diff->format('%a')) {
            $date .= $diff->format(' %a days');
        }

        if ($diff->format('%h')) {
            $date .= $diff->format(' %h hours');
        }

        if ($diff->format('%i')) {
            $date .= $diff->format(' %i min.');
        }

        if ($diff->format('%s')) {
            $date .= $diff->format(' %s sec.');
        }

        return trim($date . ' ' . $miliseconds . ' ms');
    }

    /**
     * Get pretty memory in any units
     *
     * @param  $memory
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
     */
    public static function getPrettyMemory($memory)
    {
        if (!$memory) {
            return '0 B';
        }
        static $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        return round($memory / pow(1024, ($i = floor(log($memory, 1024)))), 2) . ' ' .
            (isset($unit[$i]) ? $unit[$i] : 'undefined');
    }
}
