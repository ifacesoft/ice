<?php
/**
 * Ice core logger class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Helper\Console;
use Ice\Helper\Directory;
use Ice\Helper\File;
use Ice\Helper\Logger as Helper_Logger;
use Ice\Helper\Memory;
use Ice\Helper\Php;

/**
 * Class Logger
 *
 * Core logger class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
class Logger
{
    use Core;

    const DANGER = 'danger';
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const DEBUG = 'debug';
    const MESSAGE = 'cli';
    const GREY = 'hidden';

    /**
     * Codes of error codes
     *
     * @var array
     */
    public static $errorCodes = [
        -1 => 'FATAL',
        0 => 'ERROR',
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
    ];

    /**
     * Codes of error types
     * @var array
     */
    public static $errorTypes = [
        self::INFO => [1024],
        self::WARNING => [2, 8, 512, 8192, 16384],
        self::DANGER => [0, 1, 4, 16, 32, 64, 128, 256, 2048, 4096]
    ];

    /**
     * Map of message type and colors in cli output
     *
     * @var array
     */
    public static $consoleColors = [
        self::INFO => Console::C_CYAN,
        self::WARNING => Console::C_YELLOW,
        self::DANGER => Console::C_RED,
        self::SUCCESS => Console::C_GREEN,
        self::DEBUG => Console::C_CYAN,
        self::MESSAGE => Console::C_MAGENTA,
        self::GREY => Console::C_GRAY
    ];

    /**
     * Message log stack
     *
     * @var array
     */
    private static $log = [];


    /**
     * Useful work of application: sql, actions, etc
     *
     * @var float
     */
    private static $_usefulWork = 0;


    /**
     * Target Logger
     *
     * @var string
     */
    private $class;

    /**
     * Private constructor for logger object
     *
     * @param string $class Class (Logger for this class)
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * Initialization logger
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function init()
    {
        error_reporting(E_ALL | E_STRICT);

        ini_set('display_errors', !Environment::isProduction());

        set_error_handler('Ice\Core\Logger::errorHandler');
        register_shutdown_function('Ice\Core\Logger::shutdownHandler');

        ini_set('xdebug.var_display_max_depth', -1);
        ini_set('xdebug.profiler_enable', 1);
        ini_set('xdebug.profiler_output_dir', ROOT_DIR . 'xdebug');

        ob_start();
    }

    /**
     * Method of shutdown handler
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function shutdownHandler()
    {
        if ($error = error_get_last()) {
            if (!headers_sent()) {
                header('HTTP/1.0 500 Internal Server Error');
            }

            self::errorHandler($error['type'], $error['message'], $error['file'], $error['line'], debug_backtrace());
            self::renderLog();
        }

        session_write_close();

        if (Request::isAjax()) {
            Logger::fb('Terminated. Bye-bye...');
            exit;
        } else {
            die('Terminated. Bye-bye...' . "\n");
        }
    }

    /**
     * Method of error handler
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @param $errcontext
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if (
            $errno == E_WARNING && strpos($errstr, 'filemtime():') !== false ||
            $errno == E_WARNING && strpos($errstr, 'mysqli::real_connect():') !== false
        ) {
            Logger::getLogger()->info($errstr, self::WARNING, false);
            return; // подавляем ошибку смарти и ошибку подключения mysql (пароль в открытом виде)
        }

        self::getInstance()->error($errstr, $errfile, $errline, null, $errcontext, $errno);
    }

    /**
     * Info message
     *
     * @param $message
     * @param string|null $type
     * @param bool $isResource
     * @param bool $logging
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function info($message, $type = null, $isResource = true, $logging = true)
    {
        if (!$type) {
            $type = self::INFO;
        }

        if ($isResource) {
            /** @var Core $class */
            $class = $this->class;
            $params = null;
            if (is_array($message)) {
                list($message, $params) = $message;
            }

            $message = $class::getResource()->get($message, $params);
        }

        $logFile = Directory::get(LOG_DIR) . date('Y-m-d') . '/INFO.log';
        File::createData($logFile, $message, false, FILE_APPEND);

        Logger::fb($message, 'INFO');

        if (Request::isCli()) {
            $message = Console::getText(' ' . $message . ' ', Console::C_BLACK, self::$consoleColors[$type]) . "\n";
            echo $message;
            return $logging ? $message : '';
        } else {
            $message = '<div class="alert alert-' . $type . '">' . $message . '</div>';
            return $logging ? self::addLog($message) : $message;
        }
    }

    public static function fb($value, $type = 'LOG')
    {
        if (!Request::isCli() && !headers_sent()) {
            fb($value, $type);
        }
    }

    /**
     * Add message into log stack
     *
     * @param $message
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function addLog($message)
    {
        return self::$log[] = $message;
    }

    /**
     * Error message this exception stacktrace
     *
     * @param $message
     * @param $file
     * @param $line
     * @param \Exception $e
     * @param null $errcontext
     * @param int $errno
     * @return null|string
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function error($message, $file, $line, \Exception $e = null, $errcontext = null, $errno = E_USER_ERROR)
    {
        if (empty($errno)) {
            $errno = E_USER_ERROR;
        }

        $exception = $this->createException($message, $file, $line, $e, $errcontext, $errno);

        $output = [
            'time' => date('H:i:s'),
            'host' => Request::host(),
            'uri' => Request::uri(),
            'referer' => Request::referer(),
            'lastTemplate' => View_Render::getLastTemplate()
        ];

        Helper_Logger::outputFile($exception, $output);
        Helper_Logger::outputFb($exception, $output);

        $message = Helper_Logger::getMessage($exception);

        if (Request::isCli()) {
            echo $message;
            return $message;
        } else {
            return self::addLog($message);
        }
    }

    /**
     * Create ice exception
     *
     * @param $message
     * @param $file
     * @param $line
     * @param \Exception $e
     * @param null $errcontext
     * @param int $errno
     * @return Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function createException($message, $file, $line, \Exception $e = null, $errcontext = [], $errno = 1)
    {
        $message = (array)$message;
        if (!isset($message[1])) {
            $message[1] = [];
        }
        $message[2] = $this->class;

        return new Exception($message, $errcontext, $e, $file, $line, $errno);
    }

    /**
     * Return new instance of logger
     *
     * @param string $class
     * @return Logger
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getInstance($class = __CLASS__)
    {
        return new Logger($class);
    }

    /**
     * Output all stored logs into standard output
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function renderLog()
    {
        if (!Request::isAjax()) {
            echo implode('', self::$log);
        }
    }

    /**
     * Debug variables with die application
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function debugDie()
    {
        foreach (func_get_args() as $arg) {
            self::debug($arg);
        }

        if (!Request::isAjax()) {
            echo '<pre>';
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
     * @since 0.0
     */
    public static function debug($arg)
    {
        foreach (func_get_args() as $arg) {
            $var = stripslashes(Php::varToPhpString($arg));

            if (!Request::isAjax()) {
                echo Request::isCli()
                    ? Console::getText($var, Console::C_CYAN) . "\n"
                    : '<div class="alert alert-' . self::INFO . '">' . highlight_string('<?php // Debug value:' . "\n" . $var, true) . '</div>';

                $logFile = Directory::get(LOG_DIR) . date('Y-m-d') . '/DEBUG.log';
                File::createData($logFile, $var, false, FILE_APPEND);
            }

            Logger::fb($arg, 'DEBUG');
        }
    }

    /**
     * Clear all stored logs
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function clearLog()
    {
        self::$log = [];
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
    public static function microtime()
    {
        return microtime(true);
    }

    /**
     * Return delta time
     *
     * @param float $start Start time point
     * @param bool $usefulWork
     * @return float
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function microtimeResult($start, $usefulWork = false)
    {
        $time = microtime(true) - $start;

        if ($usefulWork) {
            Logger::$_usefulWork += $time;
        }

        return round($time, 5) * 1000 . ' ms';
    }

    public static function getUsefulWork($pretty = false)
    {
        return $pretty ? round(Logger::$_usefulWork, 5) * 1000 . ' ms' : Logger::$_usefulWork;
    }

    /**
     * Notice
     *
     * @param $message
     * @param $file
     * @param $line
     * @param \Exception $e
     * @param null $errcontext
     * @param int $errno
     * @return null|string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function notice($message, $file, $line, \Exception $e = null, $errcontext = null, $errno = E_USER_NOTICE)
    {
        return $this->error($message, $file, $line, $e, $errcontext, $errno);
    }

    /**
     * Warning
     *
     * @param $message
     * @param $file
     * @param $line
     * @param \Exception $e
     * @param null $errcontext
     * @param int $errno
     * @return null|string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function warning($message, $file, $line, \Exception $e = null, $errcontext = null, $errno = E_USER_WARNING)
    {
        return $this->error($message, $file, $line, $e, $errcontext, $errno);
    }

    /**
     * Fatal - throw ice exception
     *
     * @param $message
     * @param $file
     * @param $line
     * @param \Exception $e
     * @param null $errcontext
     * @param int $errno
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function fatal($message, $file, $line, \Exception $e = null, $errcontext = null, $errno = -1)
    {
        throw $this->createException($message, $file, $line, $e, $errcontext, $errno);
    }

    /**
     * Log
     *
     * @param $message
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function log($message)
    {
        if (!Environment::isProduction()) {
            if (Request::isCli()) {
                Query::getLogger()->info($message . ' ' . Memory::memoryGetUsagePeak(), Logger::SUCCESS, false);
            } else {
                Logger::fb($message);
            }
        }
    }
}