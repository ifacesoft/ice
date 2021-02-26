<?php
/**
 * Ice core exception class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use ErrorException;
use Ice\Core;
use Ice\Core\Resource;

/**
 * Class Exception
 *
 * Ice application exception
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class Exception extends ErrorException
{
    use Core;

    /**
     * Error context data
     * @var array
     */
    private $errorContext = null;

    /**
     * Constructor exception of ice application
     *
     * Simple constructor for fast throws Exception
     *
     * @param string|array $message
     * @param array|null $errcontext context data of exception
     * @param \Exception $previous previous exception if exists
     * @param string $errfile filename where throw Exception
     * @param int $errline number of line where throws Exception
     * @param int $errno code of error exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     * @throws \Ice\Exception\FileNotFound
     */
    public function __construct(
        $message,
        $errcontext = [],
        $previous = null,
        $errfile = null,
        $errline = null,
        $errno = 0
    )
    {
        $this->errorContext = $errcontext;

        /** @var Resource $resource */
        $resource = Resource::create(__CLASS__);

        $message = (array)$message;

        $params = null;
        $class = null;
        switch (count($message)) {
            case 2:
                list($message, $params) = $message;
                break;
            case 3:
                list($message, $params, $class) = $message;
                break;
            default:
                $message = reset($message);
                break;
        }

        if ($message) {
            $message = $resource->get($message, $params, $class);
//            $message = print_r([$message, $params, $class], true);
//            var_dump($message);
        }

        if (!$errfile) {
            $debug = debug_backtrace();

            if (!empty($debug)) {
                /**
                 * @var Exception $exception
                 */
                $exception = reset($debug)['object'];
                $errfile = $exception->getFile();
                $errline = $exception->getLine();
            }
        }

        parent::__construct($message, $errno, 1, $errfile, $errline, $previous);
    }

    /**
     * Return error context data
     *
     * Data in moment throws exception
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.0
     */
    public function getErrorContext()
    {
        return $this->errorContext;
    }

    /**
     * Ice 2 using
     * 
     * @return array|null
     */
    public function getContext() {
        return $this->errorContext;
    }
}
