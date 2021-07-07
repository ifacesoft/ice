<?php
/**
 * Ice exception http forbidden class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Exception;

/**
 * Class Http_Forbidden
 *
 * Implements page not found exception
 *
 * @see \Ice\Core\Exception
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Exception
 */
class Http_Conflict extends Http
{
    public function __construct($message = 'Conflict', array $errcontext = [], \Exception $previous = null, $errfile = null, $errline = null, $errno = 403)
    {
        parent::__construct($message, $errcontext, $previous, $errfile, $errline, $errno);
    }

    public function getHttpCode()
    {
        return 409;
    }

    public function getHttpMessage()
    {
        return 'Conflict';
    }
}
