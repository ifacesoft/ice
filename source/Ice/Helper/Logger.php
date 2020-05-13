<?php
/**
 * Ice helper logger class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Console as Core_Console;
use Ice\Core\Environment;
use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;
use Ice\Core\Render;
use Ice\Core\Request;
use Ice\Core\Request as Core_Request;
use Ice\DataProvider\Request as DataProvider_Request;
use Ice\Render\Php as Render_Php;

/**
 * Class Logger
 *
 * Helper for logger
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 */
class Logger
{
    /**
     * Output firephp messages into browser firebug console
     *
     * @param \Exception $exception
     * @param $output
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function outputFb($exception, $output)
    {
        if (Environment::getInstance()->isProduction()) {
            return;
        }

        $e = $exception->getPrevious();

        if ($e) {
            self::outputFb($e, $output);
        }

        $errcontext = $exception instanceof Exception
            ? (array)Profiler::getVar(memory_get_usage(), $exception->getErrorContext())
            : [];

        $output['message'] = Core_Logger::getErrorType($exception->getCode()) . ': ' . $exception->getMessage();
        $output['errPoint'] = '(' . $exception->getFile() . ':' . $exception->getLine() . ')';
        $output['errcontext'] = $errcontext;

        Core_Logger::fb($output['message'] . ' ' . $output['errPoint'], 'error', 'ERROR');
        Core_Logger::fb($errcontext, 'error', 'INFO');
        Core_Logger::fb($exception, 'error', 'EXCEPTION');
    }

    /**
     * InfoSave messages into log file
     *
     * @param \Exception $exception
     * @param $output
     *
     * @param $class
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.9
     * @since   0.0
     */
    public static function outputFile($exception, $output, $class)
    {
        $e = $exception->getPrevious();

        if ($e) {
            self::outputFile($e, $output, $class);
        }

        $errcontext = $exception instanceof Exception
            ? Profiler::getVar(memory_get_usage(), $exception->getErrorContext(), true)
            : '';

        $output['message'] = Core_Logger::getErrorType($exception->getCode()) . ': ' . $exception->getMessage();
        $output['errPoint'] = '(' . $exception->getFile() . ':' . $exception->getLine() . ')';
        $output['errcontext'] = $errcontext;
        $output['stackTrace'] = $exception->getTraceAsString();

        $name = Request::isCli() ? Core_Console::getCommand(null) : Request::uri();

        $logFile = Directory::get(\getLogDir() . date('Y-m-d')) .
            Core_Logger::getErrorType($exception->getCode()) . '/' . Class_Object::getClassName($class) . '/' . urlencode($name) . '.log';

        if (strlen($logFile) > 255) {
            $logFilename = substr($logFile, 0, 255 - 11);
            $logFile = $logFilename . '_' . crc32(substr($logFile, 255 - 11));
        }

        File::createData(
            $logFile,
            Render_Php::getInstance()->fetch(Core_Logger::getClass() . '/File', $output),
            false,
            FILE_APPEND
        );
    }

//    public static function outputDb($exception)
//    {
//        $params = [
//            'ip' => Request::ip(),
//            'agent' => Request::agent(),
//            'referer' => Request::referer(),
//            'host' => Request::host(),
//            'uri' => Request::uri(),
//            'post__json' => Json::encode(Request::getParam()),
//            'exception__json' => Logger::getMessage($exception)
//        ];
//
//        if (function_exists('curl_init')) {
//            $ch = curl_init();
//
//            curl_setopt($ch, CURLOPT_URL, Core_Logger::getConfig()->get('apiHost') . "/api/log/error");
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
//
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//            $server_output = curl_exec($ch);
//
//            unset($server_output);
//
//            curl_close($ch);
//        }
//
////        try {
////            Log_Error::create($params)->save();
////        } catch (\Exception $e) {}
//    }

    /**
     * Return message data from exception
     *
     * @param  \Exception $exception
     * @param  null $previousMessage
     * @param  int $level
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     * @throws \Exception
     */
    public static function getMessage($exception, $previousMessage = null, $level = 1)
    {
        $errcontext = $exception instanceof Exception
            ? Profiler::getVar(memory_get_usage(), $exception->getErrorContext(), true)
            : '';

        $type = Core_Logger::DANGER;

        foreach (Core_Logger::$errorTypes as $errorType => $errorCodes) {
            if (in_array($exception->getCode(), $errorCodes)) {
                $type = $errorType;
                break;
            }
        }

        $output = [
            'time' => date('H:i:s'),  // todo: check to right time (timezone) - double Core_Logger
            'lastTemplate' => Render::getLastTemplate(),
            'message' => Core_Logger::getErrorType($exception->getCode()) . ': ' . $exception->getMessage(),
            'errPoint' => $exception->getFile() . ':' . $exception->getLine(),
            'errcontext' => $errcontext,
            'stackTrace' => str_replace('): ', '): ' . "\n" . str_repeat("\t", $level), $exception->getTraceAsString()),
            'type' => $type,
            'previous' => $previousMessage,
            'level' => $level
        ];

        $request = DataProvider_Request::getInstance();

        $output = array_merge($output, $request->get(['host', 'uri', 'referer']));

        $message = Core_Request::isCli()
            ? Render_Php::getInstance()->fetch(Core_Logger::getClass() . '/App', $output)
            : Render_Php::getInstance()->fetch(Core_Logger::getClass() . '/Http', $output);

        if ($e = $exception->getPrevious()) {
            return self::getMessage($e, $message, $level++);
        }

        return $message;
    }
}
