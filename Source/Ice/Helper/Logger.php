<?php
/**
 * Ice helper logger class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;
use Ice\Core\Module;
use Ice\Core\Request as Core_Request;
use Ice\Core\View_Render;
use Ice\View\Render\Php as View_Render_Php;

/**
 * Class Logger
 *
 * Helper for logger
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since   0.0
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
    public static function outputFb(\Exception $exception, $output)
    {
        $e = $exception->getPrevious();

        if ($e) {
            self::outputFb($e, $output);
        }

        $errcontext = $exception instanceof Exception
            ? $exception->getErrContext()
            : [];

        $output['message'] = Core_Logger::$errorCodes[$exception->getCode()] . ': ' . $exception->getMessage();
        $output['errPoint'] = '(' . $exception->getFile() . ':' . $exception->getLine() . ')';
        $output['errcontext'] = $errcontext;

        Core_Logger::fb($output['message'] . ' ' . $output['errPoint'], 'error', 'ERROR');
        if (!empty($errcontext)/* && Memory::getVarSize($errcontext) < 3500*/) {
            Core_Logger::fb($errcontext, 'error', 'INFO');
        }
        //            fb($exception, 'EXCEPTION');
        //        Core_Logger::fb($exception, 'error', 'EXCEPTION');
        Core_Logger::fb(explode("\n", $exception->getTraceAsString()), 'error', 'WARN');
    }

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
     */
    public static function getMessage(\Exception $exception, $previousMessage = null, $level = 1)
    {
        $errcontext = $exception instanceof Exception
            ? $exception->getErrContext()
            : [];

        $type = Core_Logger::DANGER;
        foreach (Core_Logger::$errorTypes as $errorType => $errorCodes) {
            if (in_array($exception->getCode(), $errorCodes)) {
                $type = $errorType;
                break;
            }
        }

        $output = [
            'time' => date('H:i:s'),
            'host' => Core_Request::host(),
            'uri' => Core_Request::uri(),
            'referer' => Core_Request::referer(),
            'lastTemplate' => View_Render::getLastTemplate(),
            'message' => Core_Logger::$errorCodes[$exception->getCode()] . ': ' . $exception->getMessage(),
            'errPoint' => $exception->getFile() . ':' . $exception->getLine(),
            'errcontext' => $errcontext,
            'stackTrace' => str_replace('): ', '): ' . "\n" . str_repeat("\t", $level), $exception->getTraceAsString()),
            'type' => $type,
            'previous' => $previousMessage,
            'level' => $level
        ];

        $message = Core_Request::isCli()
            ? View_Render_Php::getInstance()->fetch(Core_Logger::getClass() . '/Cli', $output)
            : View_Render_Php::getInstance()->fetch(Core_Logger::getClass() . '/Http', $output);

        if ($e = $exception->getPrevious()) {
            return self::getMessage($e, $message, $level++);
        }

        return $message;
    }

    /**
     * Save messages into log file
     *
     * @param \Exception $exception
     * @param $output
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function outputFile(\Exception $exception, $output)
    {
        $e = $exception->getPrevious();

        if ($e) {
            self::outputFile($e, $output);
        }

        $errcontext = $exception instanceof Exception
            ? $exception->getErrContext()
            : [];

        $output['message'] = Core_Logger::$errorCodes[$exception->getCode()] . ': ' . $exception->getMessage();
        $output['errPoint'] = '(' . $exception->getFile() . ':' . $exception->getLine() . ')';
        $output['errcontext'] = $errcontext;
        $output['stackTrace'] = $exception->getTraceAsString();

        $logFile = Directory::get(
                Module::getInstance()->get(Module::LOG_DIR) . date('Y-m-d')
            ) . Core_Logger::$errorCodes[$exception->getCode()] . '.log';

        File::createData(
            $logFile,
            View_Render_Php::getInstance()->fetch(Core_Logger::getClass() . '/File', $output),
            false,
            FILE_APPEND
        );
    }
}
