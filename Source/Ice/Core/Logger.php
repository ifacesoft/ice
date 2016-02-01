<?php
/**
 * Ice core logger class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ebs\Model\Log_Error;
use Ebs\Model\Log_User_Session;
use FirePHP;
use Ice\Core;
use Ice\Exception\Error;
use Ice\Helper\Console;
use Ice\Helper\Directory;
use Ice\Helper\File;
use Ice\Helper\Http;
use Ice\Helper\Logger as Helper_Logger;
use Ice\Helper\Object;
use Ice\Helper\Profiler as Helper_Profiler;
use Ice\Core\Console as Core_Console;

/**
 * Class Logger
 *
 * Core logger class
 *
 * @see Ice\Core\Container
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
     * @version 0.0
     * @since   0.0
     */
    public static function init()
    {
        error_reporting(E_ALL | E_STRICT);

        ini_set('display_errors', !Environment::getInstance()->isProduction());

        self::$reserveMemory = str_repeat(' ', pow(2, 15));

        ini_set('xdebug.var_display_max_depth', -1);
        ini_set('xdebug.profiler_enable', 1);
        ini_set('xdebug.max_nesting_level', 200);
        ini_set('xdebug.profiler_output_dir', Module::getInstance()->get('logDir') . 'xdebug');

        ob_start();
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

        if ($error = error_get_last()) {
            Http::setHeader(Http::getStatusCodeHeader(500), true, 500);
            self::errorHandler($error['type'], $error['message'], $error['file'], $error['line'], []);
            self::renderLog();
        }

        session_write_close();
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
     * @since   0.0
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if ($errno == E_WARNING && strpos($errstr, 'filemtime():') !== false
            || $errno == E_WARNING && strpos($errstr, 'mysqli::real_connect():') !== false
        ) {
            return; // подавляем ошибку смарти и ошибку подключения mysql (пароль в открытом виде)
        }

        self::getInstance()->error($errstr, $errfile, $errline, null, $errcontext, $errno);
    }

    public static function getLogUserSession()
    {
        return Log_User_Session::create([
            'ip' => Request::ip(),
            'agent' => Request::agent(),
            'session' => Session::id(),
            'user' => Security::getInstance()->getUser()
        ])->save(true);
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
     * Info message
     *
     * @param  $message
     * @param  string|null $type
     * @param  bool $isResource
     * @param  bool $logging
     * @return string
     *
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

            $message = Resource::create($class)->get($message, $params);
        }

        $name = Request::isCli() ? Core_Console::getCommand(null) : Request::uri();
        $logFile = Directory::get(Module::getInstance()->get('logDir')) . date('Y-m-d') . '/INFO/' . urlencode($name) . '.log';

        if (strlen($logFile) > 255) {
            $logFilename = substr($logFile, 0, 255 - 11);
            $logFile = $logFilename . '_' . crc32(substr($logFile, 255 - 11));
        }

        $message = print_r($message, true);

        File::createData($logFile, $message . "\n", false, FILE_APPEND);

        if (Request::isCli()) {
            $message = Console::getText(' ' . $message . ' ', Console::C_BLACK, self::$consoleColors[$type]) . "\n";
            fwrite(STDOUT, $message);
            return $logging ? $message : '';
        } else {
            Logger::fb($message, 'info', $this->getFbType($type));

            $message = '<div class="alert alert-' . $type . '">' . $message . '</div>';
            return $logging ? self::addLog($message) : $message;
        }
    }

    /**
     *
     * @param $value
     * @param string $type (LOG|INFO|WARN|ERROR|DUMP|TRACE|EXCEPTION|TABLE|GROUP_START|GROUP_END)
     * @param string $label
     * @param array $options
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   0.0
     */
    public static function fb($value, $label = null, $type = 'LOG', $options = [])
    {
        if (
            Request::isCli() ||
            Environment::getInstance()->isProduction() ||
            headers_sent() ||
            !Loader::load('FirePHP', false)
        ) {
            return;
        }

        $varSize = Helper_Profiler::getVarSize($value);

        if ($varSize > pow(2, 18)) {
            FirePHP::getInstance(true)->fb('Too big data: ' . $varSize . ' bytes (max: ' . pow(2, 17) . ')', $label, 'WARN', $options);
            return;
        }

        FirePHP::getInstance(true)->fb($value, $label, $type, $options);
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
     * Error message this exception stacktrace
     *
     * @param  $message
     * @param  $file
     * @param  $line
     * @param  \Exception $e
     * @param  null $errcontext
     * @param  int $errno
     * @return null|string
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function error($message, $file, $line, \Exception $e = null, $errcontext = null, $errno = 0)
    {
        $exception = $this->createException($message, $file, $line, $e, $errcontext, (int)$errno);

        $output = [
            'time' => date('H:i:s'),
            'host' => Request::host(),
            'uri' => Request::uri(),
            'referer' => Request::referer(),
            'lastTemplate' => Render::getLastTemplate()
        ];

        Helper_Logger::outputFile($exception, $output, $this->class);
//        Helper_Logger::outputDb($exception);
        Helper_Logger::outputFb($exception, $output);

        $message = Helper_Logger::getMessage($exception);

        $logError = Log_Error::create([
            '/message' => $exception->getMessage(),
            'exception' => $message,
            'environment' => Environment::getInstance()->getName(),
            'error_type' => Logger::$errorCodes[$exception->getCode()]
        ]);

        $this->save($logError);

        if (Request::isCli()) {
            fwrite(STDOUT, $message . "\n");
            return $message;
        } else {
            return Logger::addLog($message);
        }
    }

    /**
     * Create ice exception
     *
     * @param  $message
     * @param  $file
     * @param  $line
     * @param  \Exception $e
     * @param  array|null $errcontext
     * @param  int $errno
     * @param  string $exceptionClass
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
        \Exception $e = null,
        $errcontext = [],
        $errno = 0,
        $exceptionClass = 'Ice:Error'
    )
    {
        $message = (array)$message;
        if (!isset($message[1])) {
            $message[1] = [];
        }
        $message[2] = $this->class;

        /**
         * @var Exception $exceptionClass
         */
        $exceptionClass = Object::getClass(Exception::getClass(), $exceptionClass);

        if (is_array($errcontext) && isset($errcontext['e'])) {
            unset($errcontext['e']);
        }

        return new $exceptionClass($message, $errcontext, $e, $file, $line, $errno);
    }

    /**
     * Return new instance of logger
     *
     * @param  string $class
     * @return Logger
     *
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

    /**
     * Notice
     *
     * @param  $message
     * @param  $file
     * @param  $line
     * @param  \Exception $e
     * @param  null $errcontext
     * @param  int $errno
     * @return null|string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function notice($message, $file, $line, \Exception $e = null, $errcontext = null, $errno = E_USER_NOTICE)
    {
        return $this->error($message, $file, $line, $e, $errcontext, $errno);
    }

    /**
     * Warning
     *
     * @param  $message
     * @param  $file
     * @param  $line
     * @param  \Exception $e
     * @param  null $errcontext
     * @param  int $errno
     * @return null|string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function warning($message, $file, $line, \Exception $e = null, $errcontext = null, $errno = E_USER_WARNING)
    {
        return $this->error($message, $file, $line, $e, $errcontext, $errno);
    }

    /**
     * Fatal - throw ice exception
     *
     * @param  $message
     * @param  $file
     * @param  $line
     * @param  \Exception $e
     * @param  null $errcontext
     * @param  int $errno
     * @param  string $exceptionClass
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
        \Exception $e = null,
        $errcontext = null,
        $errno = -1,
        $exceptionClass = 'Ice:Error'
    )
    {
        throw $this->createException($message, $file, $line, $e, $errcontext, $errno, $exceptionClass);
    }

    public static function log($value, $label = null, $type = 'LOG', $options = [])
    {
        $value = str_replace(["\n", "\t"], ' ', $value);

        $name = Request::isCli() ? Core_Console::getCommand(null) : Request::uri();
        $logFile = Directory::get(Module::getInstance()->get('logDir')) . date('Y-m-d') . '/LOG/' . urlencode($name) . '.log';

        if (strlen($logFile) > 255) {
            $logFilename = substr($logFile, 0, 255 - 11);
            $logFile = $logFilename . '_' . crc32(substr($logFile, 255 - 11));
        }

        File::createData($logFile, $label . ': ' . $value . "\n\n", false, FILE_APPEND);

        if (Environment::getInstance()->isProduction()) {
            return;
        }

        if (Request::isCli()) {
            $colors = [
                'INFO' => Console::C_GREEN,
                'DUMP' => Console::C_CYAN,
                'WARN' => Console::C_YELLOW,
                'ERROR' => Console::C_RED,
                'LOG' => Console::C_CYAN,
            ];

            $message = Console::getText($label . ': ' . $value, Console::C_BLACK, $colors[$type]) . "\n";
            fwrite(STDOUT, $message);
        } else {
            Logger::fb($value, $label, $type, $options);
        }
    }

    /**
     * @param Model $log
     */
    public function save($log)
    {
        $logUserSession = Logger::getLogUserSession();

        $logUserSession__fk = $log->get('log_user_session__fk', false);

        if ($logUserSession__fk && $logUserSession__fk != $logUserSession->getPkValue()) {
            $logUserSession->set(['log_user_session__fk' => $logUserSession__fk])->save();
            $log->set(['log_user_session' => $logUserSession])->save();
        } else {
            $log->set([
                'logger_class' => $this->class,
                'log_user_session' => $logUserSession
            ])->save();
        }
    }
}
