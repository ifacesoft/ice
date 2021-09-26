<?php
/**
 * Ice core logger class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use ChromePhp;
use Ice\Core;
use Ice\DataProvider\Request as DataProvider_Request;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Helper\Console as Helper_Console;
use Ice\Helper\Date;
use Ice\Helper\File;
use Ice\Helper\Logger as Helper_Logger;
use Ice\Helper\Class_Object;
use Ice\Helper\Profiler as Helper_Profiler;
use Ice\Helper\Type_String;
use Ice\Model\Log_Error;
use Exception;
use Ifacesoft\Ice\Core\Infrastructure\Core\Application;
use Throwable;

/**
 * Class Logger
 *
 * Core logger class
 *
 * @see \Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since   0.0
 */
class Logger
{
    use Core;

    /** PSR-3 */
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';

    const SUCCESS = 'success';

    /** @deprecated 1.5 */
    const DANGER = 'danger';
    /** @deprecated 1.5 */
    const MESSAGE = 'cli';
    /** @deprecated 1.5 */
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
        self::INFO => Helper_Console::C_CYAN,
        self::WARNING => Helper_Console::C_YELLOW,
        self::DANGER => Helper_Console::C_RED,
        self::SUCCESS => Helper_Console::C_GREEN,
        self::DEBUG => Helper_Console::C_CYAN,
        self::MESSAGE => Helper_Console::C_MAGENTA,
        self::GREY => Helper_Console::C_GRAY
    ];

    /**
     * Message log stack
     *
     * @var array
     */
    private static $log = [];
    private static $reserveMemory = null;
    /**
     * Target Logger
     *
     * @var string
     */
    private $class = null;

    /**
     * Private constructor for logger object
     *
     * @param string $class Class (Logger for this class)
     *
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    private function __construct($class)
    {
        if (!$class) {
            throw new Error('Class for logger not defined');
        }

        $this->class = $class;
    }

    /**
     * Initialization logger
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.5
     * @since   0.0
     */
    public static function init()
    {
        self::$reserveMemory = str_repeat('#', pow(2, 25));

        if (Environment::getInstance()->isProduction()) {
            error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_USER_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);
            ini_set('display_errors', 0);

            return;
        }

        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED); // Оставить только E_ALL
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    }

    /**
     * Method of shutdown handler
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function shutdownHandler()
    {
        self::$reserveMemory = null;
//        gc_collect_cycles();

        //todo: Response output mast by here
        if ($error = error_get_last()) {
//            Http::setStatusCodeHeader(500);
            $error['message'] .= ' [peak: ' . Helper_Profiler::getPrettyMemory(memory_get_peak_usage(true)) . ']';

            self::errorHandler($error['type'], $error['message'], $error['file'], $error['line'], []);
            self::renderLog();
        }

        if (session_status() !== PHP_SESSION_NONE) {
            session_write_close();
        }

        if (ob_get_level()) {
            ob_get_flush();
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
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.0
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext = [])
    {
        $lowLevelErrors = [E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_STRICT, E_DEPRECATED, E_USER_DEPRECATED];

        if (!Environment::getInstance()->isDevelopment() && in_array($errno, $lowLevelErrors)) {
            return;
        }

        // Выпилить
        if (in_array($errno, [E_USER_DEPRECATED, E_DEPRECATED])) {
            return;
        }

        if (($errno == E_WARNING && strpos($errstr, 'filemtime():') !== false)
            || ($errno == E_WARNING && strpos($errstr, 'mysqli::real_connect():') !== false)
            || ($errno == E_WARNING && strpos($errstr, 'ob_get_flush():') !== false)
            || ($errno == E_WARNING && strpos($errstr, 'zend.assertions') !== false)
        ) {
            return; // подавляем ошибку смарти и ошибку подключения mysql (пароль в открытом виде)
        }

        self::getInstance()->error($errstr, $errfile, $errline, null, $errcontext, $errno);
    }

    /**
     * Error message this exception stacktrace
     *
     * @param  $message
     * @param  $file
     * @param  $line
     * @param Exception|Throwable $e
     * @param null $errcontext
     * @param int $errno
     * @return string
     * @throws Exception
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function error($message, $file, $line, $e = null, $errcontext = null, $errno = 0)
    {
        if (
            Environment::getInstance()->isProduction()
            && in_array($errno, [E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_STRICT, E_DEPRECATED, E_USER_DEPRECATED])) {
            return '';
        }

        $exception = $this->createException($message, $file, $line, $e, $errcontext, (int)$errno);

        $exceptionText = Helper_Logger::getMessage($exception);

        $output = [
            'time' => \date('H:i:s') . ' ' . microtime(true), // todo: check to right time (timezone)
            'lastTemplate' => Render::getLastTemplate()
        ];

        $request = DataProvider_Request::getInstance();

        $output = array_merge($output, $request->get(['host', 'uri', 'referer']));

        Helper_Logger::outputFile($exception, $output, $this->class);
//        Helper_Logger::outputDb($exception);
        Helper_Logger::outputFb($exception, $output);

        $environment = Environment::getInstance();

//        if (Module::$loaded) {
        $logError = Log_Error::create([
            'message' => Type_String::truncate($exception->getMessage(), 255),
            'exception' => $exceptionText,
            'hostname' => $environment->getHostname(),
            'environment' => $environment->getName(),
            'error_type' => Logger::getErrorType($exception->getCode()),
            'request_type' => Request::isCli() ? 'cli' : (Request::isAjax() ? 'ajax' : 'http'),
            'request_data' => $_REQUEST,
            'request_string' => $request->get('uri'),
            'request_method' => $request->get('method'),
            'error_context' => $exception->getErrorContext(),
            'session__fk' => session_id()
        ]);

        $this->save($logError);
//        }

        if (Request::isCli()) {
            fwrite(STDOUT, Application::debugExceptionString($exception, 0, 'cli'));
        } else {
            Logger::addLog($exceptionText);
        }

        return $message;
    }

    /**
     * Create ice exception
     *
     * @param  $message
     * @param  $file
     * @param  $line
     * @param Exception $e
     * @param array|null $errcontext
     * @param int $errno
     * @param string $exceptionClass
     * @return Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    private function createException(
        $message,
        $file,
        $line,
        $e = null,
        $errcontext = [],
        $errno = 0,
        $exceptionClass = 'Ice:Error'
    )
    {
//        $message = (array)$message;
//        if (!isset($message[1])) {
//            $message[1] = [];
//        }
//        $message[2] = $this->class;

        /**
         * @var Exception $exceptionClass
         */
        $exceptionClass = Class_Object::getClass(\Ice\Core\Exception::getClass(), $exceptionClass);

        if (is_array($errcontext) && isset($errcontext['e'])) {
            unset($errcontext['e']);
        }

        return new $exceptionClass($message, $errcontext, $e, $file, $line, $errno);
    }

    /**
     * @param Model $log
     */
    public function save($log)
    {
        try {
            $log->set([
                    'logger_class' => $this->class,
                    'session' => session_id()
                ])->save();
        } catch (Exception $e) {

        } catch (Throwable $e) {

        }
    }

    /**
     * Add message into log stack
     *
     * @param  $message
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function addLog($message)
    {
        return self::$log[] = $message;
    }

    /**
     * Return new instance of logger
     *
     * @param string $class
     * @return Logger
     *
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
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
     * @since   0.0
     */
    public static function renderLog()
    {
        if (!Request::isAjax() && !Environment::getInstance()->isProduction()) {
            echo implode('', self::$log);
        }
    }

    /**
     * Clear all stored logs
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function clearLog()
    {
        self::$log = [];
    }

    public static function log($value, $label = null, $type = 'LOG', $options = [])
    {
        $value = str_replace(["\n", "\t"], ' ', $value);

        if ($pos = strrpos($type, '_')) {
            $typePath = substr($type, 0, $pos);
            $type = substr($type, $pos + 1);
        } else {
            $typePath = $type;
        }

        if (Environment::getInstance()->isDevelopment()) {
            $name = Request::isCli() ? Console::getCommand(null) : Request::uri();
            $logFile = getLogDir() . \date('Y-m-d_H') . '/' . $typePath . '/' . urlencode($name) . '.log';

            if (strlen($logFile) > 255) {
                $logFilename = substr($logFile, 0, 255 - 11);
                $logFile = $logFilename . '_' . crc32(substr($logFile, 255 - 11));
            }

            File::createData($logFile, '[' . \date(Date::FORMAT_MYSQL) . ' ' . microtime(true) . '] ' . $label . ': ' . $value . "\n\n", false, FILE_APPEND);
        }

        if (Request::isCli()) {
            $colors = [
                'INFO' => Helper_Console::C_GREEN,
                'DUMP' => Helper_Console::C_CYAN,
                'WARN' => Helper_Console::C_YELLOW,
                'ERROR' => Helper_Console::C_RED,
                'LOG' => Helper_Console::C_CYAN,
            ];

            $message = Helper_Console::getText($label . ': ' . $value, Helper_Console::C_BLACK, $colors[$type]) . "\n";
            fwrite(STDOUT, $message);
        } else {
            Logger::fb($value, $label, $type, $options);
        }
    }

    /**
     *
     * @param $value
     * @param null $label
     * @param string $type (LOG|INFO|WARN|ERROR|DUMP|TRACE|EXCEPTION|TABLE|GROUP_START|GROUP_END)
     * @param array $options
     *
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     * @version 1.13
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     */
    public static function fb($value, $label = null, $type = 'LOG', $options = [])
    {
        $firePhp = VENDOR_DIR . 'ccampbell/chromephp/ChromePhp.php';

        if (Request::isCli() || Environment::getInstance()->isProduction() || headers_sent() || !is_file($firePhp)) {
            return;
        }

        if (!class_exists('FirePHP', false) && is_file($firePhp)) {
            require_once $firePhp;
        }

        switch (strtolower($type)) {
            case ChromePhp::WARN:
                ChromePhp::warn(Helper_Profiler::getVar(memory_get_usage(), $value));
                break;
            case ChromePhp::ERROR:
                ChromePhp::error(Helper_Profiler::getVar(memory_get_usage(), $value));
                break;
            case ChromePhp::INFO:
                ChromePhp::info(Helper_Profiler::getVar(memory_get_usage(), $value));
                break;
            default:
                ChromePhp::log(Helper_Profiler::getVar(memory_get_usage(), $value));
        }
    }

    /**
     * Info message
     *
     * @param  $message
     * @param string|null $type
     * @param bool $isResource @todo: передаем сюда сам объект Resource или null (Статически типизуруем аргумент в методе)
     * @param bool $logging
     * @return string
     *
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function info($message, $type = Logger::INFO, $isResource = false, $logging = true)
    {
        if (!$type) {
            $type = self::INFO;
        }

        if ($isResource) {
            /**
             * @var Core $class
             */
            $class = $this->class;
            $params = null;

            if (is_array($message)) {
                list($message, $params) = $message;
            }

            if ($isResource instanceof Resource) {
                $isResource->get($message, $params);
            } else {
                $message = Resource::create($class)->get($message, $params);
            }
        }

        if (Environment::getInstance()->isDevelopment()) {
            $name = Request::isCli() ? Console::getCommand(null) : Request::uri();
            $logFile = getLogDir() . \date('Y-m-d_H') . '/INFO/' . urlencode($name) . '.log';

            if (strlen($logFile) > 255) {
                $logFilename = substr($logFile, 0, 255 - 11);
                $logFile = $logFilename . '_' . crc32(substr($logFile, 255 - 11));
            }

            $message = print_r($message, true);

            File::createData(
                $logFile,
                '[' . \date(Date::FORMAT_MYSQL) . ' ' . microtime(true) . '] ' . $message . "\n",
                false,
                FILE_APPEND
            );
        }

        if (Request::isCli()) {
            $message = Helper_Console::getText(' ' . $message . ' ', Helper_Console::C_BLACK, self::$consoleColors[$type]) . "\n";

//            fwrite(STDOUT, $message); // ob_cache|ob_get_clean not catch stdout
            echo $message;

            return $logging ? $message : '';
        } else {
            Logger::fb($message, 'info', $this->getFbType($type));

            $message = '<div class="alert alert-' . $type . '">' . print_r($message, true) . '</div>';
            return $logging ? self::addLog($message) : $message;
        }
    }

    private function getFbType($type)
    {
        switch ($type) {
            case Logger::DANGER:
                return 'ERROR';
            case Logger::WARNING:
                return 'WARN';
            default:
                return 'INFO';
        }
    }

    /**
     * Notice
     *
     * @param  $message
     * @param  $file
     * @param  $line
     * @param Exception $e
     * @param null $errcontext
     * @param int $errno
     * @return null|string
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function notice($message, $file, $line, $e = null, $errcontext = null, $errno = E_USER_NOTICE)
    {
        return $this->error($message, $file, $line, $e, $errcontext, $errno);
    }

    /**
     * Warning
     *
     * @param  $message
     * @param  $file
     * @param  $line
     * @param Exception $e
     * @param null $errcontext
     * @param int $errno
     * @return null|string
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function warning($message, $file, $line, $e = null, $errcontext = null, $errno = E_USER_WARNING)
    {
        return $this->error($message, $file, $line, $e, $errcontext, $errno);
    }

    /**
     * Fatal - throw ice exception
     *
     * @param  $message
     * @param  $file
     * @param  $line
     * @param Exception|null $e
     * @param null $errcontext
     * @param int $errno
     * @param string $exceptionClass
     * @return null
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public function exception(
        $message,
        $file,
        $line,
        Exception $e = null,
        $errcontext = null,
        $errno = -1,
        $exceptionClass = 'Ice:Error'
    )
    {
        throw $this->createException($message, $file, $line, $e, $errcontext, $errno, $exceptionClass);
    }

    public static function getErrorType($code)
    {
        return isset(self::$errorCodes[$code]) ? Logger::$errorCodes[$code] : 'UNKNOWN_ERROR';
    }
}
