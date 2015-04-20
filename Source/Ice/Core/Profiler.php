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
    private static $profiler = [];

    public static function setPoint($name, $startTime, $startMemory)
    {
        Profiler::setTiming($name, $startTime);
        Profiler::setMemoryUsages($name, $startMemory);
    }

    /**
     * Set delta timing
     *
     * @param  string $name
     * @param  int $startTime
     * @return float
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
     */
    public static function setTiming($name, $startTime)
    {
        if (is_object($name)) {
            $name = $name->__toString();
        }

        return Profiler::$profiler[$name][Profiler::TIME] = Profiler::getMicrotimeResult($startTime);
    }

    /**
     * Return delta time
     *
     * @param  float $startTime Start time point
     * @return float
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
     */
    public static function getMicrotimeResult($startTime)
    {
        return Profiler::getMicrotime() - $startTime;
    }

    /**
     * Return current float microtime
     *
     * @return float
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
     */
    public static function getMicrotime()
    {
        return microtime(true);
    }

    /**
     * Set delta memory usage
     *
     * @param  string $name
     * @param  int $startMemory
     * @return float
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
     */
    public static function setMemoryUsages($name, $startMemory)
    {
        if (is_object($name)) {
            $name = $name->__toString();
        }

        return Profiler::$profiler[$name][Profiler::MEMORY] = Profiler::getMemoryGetUsageResult($startMemory);
    }

    /**
     * Get delta memory usage
     *
     * @param  $startMemory
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
     */
    public static function getMemoryGetUsageResult($startMemory)
    {
        return Profiler::getMemoryGetUsage() - $startMemory;
    }

    /**
     * Return memory usage
     *
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
     */
    public static function getMemoryGetUsage()
    {
        return memory_get_usage(true);
    }

    /**
     * Report profile data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.6
     * @param   $name
     * @return  string
     */
    public static function getReport($name)
    {
        $message = $name . ' [';

        if (isset(Profiler::$profiler[$name])) {
            $message .= 'Time: ' . Helper_Profiler::getPrettyTime(Profiler::$profiler[$name][Profiler::TIME]) . ' ';
        }

        if (isset(Profiler::$profiler[$name])) {
            $message .= 'Mem: ' . Helper_Profiler::getPrettyMemory(Profiler::$profiler[$name][Profiler::MEMORY]) . ' ';
        }

        return $message . '(peak: ' . Helper_Profiler::getPrettyMemory(memory_get_peak_usage(true)) . ')]';
    }
}
