<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 26.12.13
 * Time: 0:10
 */

namespace ice\core;

use ice\core\helper\Dir;
use ice\Exception;
use ice\Ice;

class Logger
{
    private static $errorTypes = array(
        0 => 'Error',
        1 => 'E_ERROR',
        2 => 'E_WARNING',
        4 => 'E_PARSE',
        8 => 'E_NOTICE',
        16 => 'E_CORE_ERROR',
        32 => 'E_CORE_WARNING',
        64 => 'E_COMPILE_ERROR',
        128 => 'E_COMPILE_WARNING',
        256 => 'E_USER_ERROR',
        512 => 'E_USER_WARNING',
        1024 => 'E_USER_NOTICE',
        2048 => 'E_STRICT',
        4096 => 'E_RECOVERABLE_ERROR',
        8192 => 'E_DEPRECATED',
        16384 => 'E_USER_DEPRECATED',
    );

    public static function init($project)
    {
        error_reporting(E_ALL | E_STRICT);

        $productionHost = Ice::getConfig()->getParam('modules/' . $project . '/productionHost');

        if (!isset($productionHost) || !isset($_SERVER['HTTP_HOST']) || $productionHost != $_SERVER['HTTP_HOST']) {
            ini_set('display_errors', 1);
        } else {
            ini_set('display_errors', 0);
        }

        set_error_handler('Ice\core\Logger::errorHandler');
        register_shutdown_function('Ice\core\Logger::shutdownHandler');

        ini_set('xdebug.var_display_max_depth', -1);

        require_once(Ice::getEnginePath() . 'Vendor/FirePHPCore/FirePHP.class.php');
        require_once(Ice::getEnginePath() . 'Vendor/FirePHPCore/fb.php');
        ob_start();
    }

    public static function shutdownHandler()
    {
        if ($error = error_get_last()) {
            if (!headers_sent()) {
                header('HTTP/1.0 500 Internal Server Error');
            }

//            Ice::get(Ice::getProject())->display();

            self::errorHandler($error['type'], $error['message'], $error['file'], $error['line'], debug_backtrace());
            die('Terminated. Bye-bye...');
        }
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        self::outputErrors(new Exception($errstr, $errcontext, null, $errfile, $errline, $errno));
    }

    public static function log($message)
    {
        $logDir = Ice::getRootPath() . 'log/' . Ice::getProject() . '/';
        $logFile = Dir::get($logDir) . 'error_' . date('Y-m-d') . '.log';
        file_put_contents($logFile, $message, FILE_APPEND);
    }

    public static function output($message)
    {
        echo $message;
    }

    public static function outputErrors(\Exception $exception)
    {
        $e = $exception->getPrevious();

        if ($e) {
            self::outputErrors($e);
        }

        $delimetr = '[' . date('Y-m-d H:i:s') . '] ' . str_repeat('-', 100);

        $errcontext = $exception instanceof Exception
            ? $exception->getErrContext()
            : array();

        $log = array();
        $log['message'] = self::$errorTypes[$exception->getCode()] . ': ' . $exception->getMessage();
        $log['errPoint'] = '(' . $exception->getFile() . ':' . $exception->getLine() . ')';
        if (!empty($errcontext)) {
            $log['errcontext'] = print_r($errcontext, true);
        }
        $log['stackTrace'] = $exception->getTraceAsString();

        Logger::log($delimetr . "\n" . implode("\n", $log) . "\n\n");

        $message = '<meta charset="utf-8"/>';
        $message .= '<div class="alert alert-danger" style="font-size: 10px;font-family: Tahoma, Geneva, sans-serif;">';
        $message .= '<strong style="color: red;">' . $log['message'] . '</strong> <em style="color: blue;">' . $log['errPoint'] . '</em><br/>';
        if (!empty($errcontext)) {
            $message .= '<a style="color:grey; text-decoration: none; border-bottom:1px dashed;" href="#" onclick="$(\'.errcontext\').show();">errcontext</a><br/>';
            $message .= '<pre class="errcontext" style="color: green; display: none">' . print_r(
                    $errcontext,
                    true
                ) . '</pre>';
        }
        $message .= nl2br($log['stackTrace'], true);
        $message .= '</div>';

        Logger::output($message);

        if (function_exists('fb')) {
            fb($delimetr);
            fb($log['message'] . $log['errPoint'], 'ERROR');
//            if (!empty($errcontext)) {
//                fb($errcontext, 'INFO');
//            }
            fb(explode("\n", $log['stackTrace']), 'WARN');
        }
    }
}