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
 * Class Http_Unauthorized
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
class Http_Unauthorized extends Http
{
    public function __construct($message = 'Unauthorized', array $errcontext = [], \Exception $previous = null, $errfile = null, $errline = null, $errno = 401)
    {
        parent::__construct($message, $errcontext, $previous, $errfile, $errline, $errno);
    }

    public function getHttpCode()
    {
        return 401;
    }

    public function getHttpMessage()
    {
        return 'Unauthorized';
    }
}
