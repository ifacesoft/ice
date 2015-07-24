<?php

namespace Ice\Core;

use Ice\Helper\Console;
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
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function dump($arg)
    {
        foreach (func_get_args() as $arg) {
            $var = stripslashes(Php::varToPhpString($arg));

            if (!Request::isAjax()) {
                if (Request::isCli()) {
                    fwrite(STDOUT, Console::getText($var, Console::C_CYAN) . "\n");
                } else {
                    echo '<div class="alert alert-' . Logger::INFO . '">' . str_replace('<span style="color: #0000BB">&lt;?php&nbsp;</span>', '', highlight_string('<?php // Debug value:' . "\n" . $var . "\n", true)) . '</div>';
                }

                $logFile = Directory::get(Module::getInstance()->get(Module::LOG_DIR)) . date('Y-m-d') . '/DEBUG.log';
                File::createData($logFile, $var, false, FILE_APPEND);
            }

            Logger::fb($arg, 'debug', 'INFO');
        }

        return $arg;
    }
}
