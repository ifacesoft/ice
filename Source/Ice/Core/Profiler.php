<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 3/21/15
 * Time: 5:58 PM
 */

namespace Ice\Core;


class Profiler {
    private static $_timing = [];
    private static $_memoryUsages = [];

    /**
     * @param string $name
     * @param int $time
     */
    public static function setTiming($name, $time) {
        Profiler::$_timing[$name] = $time;
    }

    /**
     * @param string $name
     * @param int $memory
     */
    public static function setMemoryUsages($name, $memory)
    {
        Profiler::$_memoryUsages[$name] = $memory;
    }
}