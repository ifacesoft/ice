<?php

namespace Ice\Core;

use Ice\Helper\Profiler as Helper_Profiler;
use XHProfRuns_Default;

class Profiler
{
    const TIME = 'timing';
    const MEMORY = 'memoryUsage';

    private static $isXhprofEnabled = false;

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
     * @param $postfix
     * @return string
     */
    public static function getReport($name, $postfix = '')
    {
        $message = '[';

        if (isset(Profiler::$profiler[$name])) {
            $message .= 'Time: ' . Helper_Profiler::getPrettyTime(Profiler::$profiler[$name][Profiler::TIME]) . ' ';
        }

        if (isset(Profiler::$profiler[$name])) {
            $message .= 'Mem: ' . Helper_Profiler::getPrettyMemory(Profiler::$profiler[$name][Profiler::MEMORY]) . ' ';
        }

        return $message . '(peak: ' . Helper_Profiler::getPrettyMemory(memory_get_peak_usage(true)) . ')] ' . $name . $postfix;
    }

    public static function xhprofEnable()
    {
        if (Profiler::$isXhprofEnabled) {
            Profiler::xphrofDisable();
        }

        if (function_exists('\xhprof_enable')) {
            \xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
        }

        if (function_exists('\tideways_xhprof_enable')) {
            \tideways_xhprof_enable(TIDEWAYS_XHPROF_FLAGS_MEMORY | TIDEWAYS_XHPROF_FLAGS_CPU);
        }

        Profiler::$isXhprofEnabled = true;
    }

    public static function xphrofDisable()
    {
        if (Profiler::$isXhprofEnabled) {
            $xhprof_data = null;

            if (function_exists('\xhprof_disable')) {
                $xhprof_data = xhprof_disable();
            }

            if (function_exists('\tideways_xhprof_disable')) {
                $xhprof_data = tideways_xhprof_disable();
            }

            if ($xhprof_data) {
                $xhprof_runs = new XHProfRuns_Default('/tmp');
                $xhprof_runs->save_run($xhprof_data, "xhprof_testing");
            }

            Profiler::$isXhprofEnabled = false;
        }
    }
}
