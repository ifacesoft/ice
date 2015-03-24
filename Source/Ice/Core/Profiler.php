<?php

namespace Ice\Core;

use Ice\Helper\Profiler as Helper_Profiler;

class Profiler
{
    const TIME = 'timing';
    const MEMORY = 'memoryUsage';

    /**
     * Profile data
     *
     * @var array
     */
    private static $_profiler = [];

    /**
     * Set delta timing
     *
     * @param string $name
     * @param int $startTime
     * @return float
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.6
     */
    public static function setTiming($name, $startTime)
    {
        if (is_object($name)) {
            $name = $name->__toString();
        }

        return Profiler::$_profiler[$name][Profiler::TIME] = Profiler::getMicrotimeResult($startTime);
    }

    /**
     * Set delta memory usage
     *
     * @param string $name
     * @param int $startMemory
     * @return float
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.6
     */
    public static function setMemoryUsages($name, $startMemory)
    {
       if (is_object($name)) {
           $name = $name->__toString();
       }

        return Profiler::$_profiler[$name][Profiler::MEMORY] = Profiler::getMicrotimeResult($startMemory);
    }

    /**
     * Return current float microtime
     *
     * @return float
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getMicrotime()
    {
        return microtime(true);
    }

    /**
     * Return delta time
     *
     * @param float $startTime Start time point
     * @return float
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public static function getMicrotimeResult($startTime)
    {
        return microtime(true) - $startTime;
    }

    /**
     * Return memory usage
     *
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.6
     */
    public static function getMemoryGetUsage()
    {
        return $size = memory_get_usage(true);
    }

    /**
     * Get delta memory usage
     *
     * @param $startMemory
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getMemoryGetUsageResult($startMemory)
    {
        return microtime(true) - $startMemory;
    }

    /**
     * Report profile data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.6
     */
    public static function getReport()
    {
        foreach (Profiler::$_profiler as $name => $data) {
            $message = $name . ' -';

            if (isset($data[Profiler::TIME])) {
                $message .= ' timing: ' . Helper_Profiler::getPrettyTime($data[Profiler::TIME]);
            }

            if (isset($data[Profiler::MEMORY])) {
                $message .= ' memory usage: ' . Helper_Profiler::getPrettyMemory($data[Profiler::MEMORY]);
            }

            $message .= ' (memory peak: ' . Helper_Profiler::getPrettyMemory(memory_get_peak_usage(true));

            Logger::fb($message, 'profiler', 'INFO');
        }
    }
}