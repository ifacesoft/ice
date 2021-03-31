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
     * @param $data
     *
     * @return mixed
     * @throws Exception
     * @throws Config_Error
     * @version 0.0
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     */
    public static function dump($data, $display = true, $prefix = '')
    {
        $var = stripslashes(Php::varToPhpString($data));

        $var = str_replace("\x1e", Helper_Console::getText('\x1E', Helper_Console::C_YELLOW), $var);

        fwrite(fopen('php://stdout', 'w'), Helper_Console::getText($var, Helper_Console::C_CYAN) . "\n");

        if (!Request::isCli() && $display) {
            echo '<div class="alert alert-' . Logger::INFO . '">' . str_replace('<span style="color: #0000BB">&lt;?php&nbsp;</span>', '', highlight_string('<?php // Debug value:' . "\n" . $var . "\n", true)) . '</div>';
        }

        $name = Request::isCli() ? Console::getCommand(null) : Request::uri();

        $logFile = Directory::get(
                \getLogDir() . date('Y-m-d_H') . '/' .
                '/DEBUG/'
            ) . $prefix . urlencode($name) . '.log';

        if (strlen($logFile) > 255) {
            $logFilename = substr($logFile, 0, 255 - 11);
            $logFile = $logFilename . '_' . crc32(substr($logFile, 255 - 11));
        }

        File::createData($logFile, $var, false, FILE_APPEND);

        Logger::fb($data, 'debug', 'INFO');

        return $data;
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
       return self::dump($arg, false, 'emil_');
    }
}
