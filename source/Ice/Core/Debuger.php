<?php

namespace Ice\Core;

use Ice\Exception\Config_Error;
use Ice\Helper\Console as Helper_Console;
use Ice\Helper\Directory;
use Ice\Helper\File;
use Ice\Helper\Php;

class Debuger
{
    /**
     * Debug variables with die application
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function dumpDie()
    {
        foreach (func_get_args() as $arg) {
            Debuger::dump($arg);
        }

        if (!Request::isAjax()) {
            echo '<pre>';
            Logger::renderLog();
            debug_print_backtrace();
            echo '</pre>';
        }

        exit;
    }

    /**
     * Debug variables
     *
     * @param $arg
     *
     * @return mixed
     * @throws Exception
     * @throws Config_Error
     * @version 0.0
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     */
    public static function dump($arg)
    {
        foreach (func_get_args() as $arg1) {
            $var = stripslashes(Php::varToPhpString($arg1));

            $var = str_replace("\x1e", Helper_Console::getText('\x1E', Helper_Console::C_YELLOW), $var);

            if (!Request::isAjax()) {
                if (Request::isCli()) {
                    fwrite(STDOUT, Helper_Console::getText($var, Helper_Console::C_CYAN) . "\n");
                } else {
                    echo '<div class="alert alert-' . Logger::INFO . '">' . str_replace('<span style="color: #0000BB">&lt;?php&nbsp;</span>', '', highlight_string('<?php // Debug value:' . "\n" . $var . "\n", true)) . '</div>';
                }
            }

            $name = Request::isCli() ? Console::getCommand(null) : Request::uri();

            $logFile = Directory::get(
                    \getLogDir() . date('Y-m-d_H') . '/' .
                    '/DEBUG/'
                ) . urlencode($name) . '.log';

            if (strlen($logFile) > 255) {
                $logFilename = substr($logFile, 0, 255 - 11);
                $logFile = $logFilename . '_' . crc32(substr($logFile, 255 - 11));
            }

            File::createData($logFile, $var, false, FILE_APPEND);

            Logger::fb($arg1, 'debug', 'INFO');

        }

        return $arg;
    }

    /**
     * Debug variables
     *
     * @param $arg
     *
     * @return mixed
     * @throws Exception
     * @throws Config_Error
     * @version 0.0
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     */
    public static function dumpToFile($arg)
    {
        foreach (func_get_args() as $arg1) {
            $var = stripslashes(Php::varToPhpString($arg1));

            $var = str_replace("\x1e", Helper_Console::getText('\x1E', Helper_Console::C_YELLOW), $var);

            $name = Request::isCli() ? Console::getCommand(null) : Request::uri();

            $logFile = Directory::get(
                    \getLogDir() . date('Y-m-d_H') . '/' .
                    '/DEBUG/'
                ) . urlencode($name) . '.log';

            if (strlen($logFile) > 255) {
                $logFilename = substr($logFile, 0, 255 - 11);
                $logFile = $logFilename . '_' . crc32(substr($logFile, 255 - 11));
            }

            File::createData($logFile, $var, false, FILE_APPEND);

            Logger::fb($arg1, 'debug', 'INFO');

        }

        return $arg;
    }
}
