<?php
/**
 * Ice exception page not found class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Exception;

/**
 * Class Http_Not_Found
 *
 * Implements page not found exception
 *
 * @see Ice\Core\Exception
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Exception
 */
class Http_Not_Found extends Http
{
    public function getHttpCode()
    {
        return 404;
    }

    public function getHttpMessage()
    {
        return 'Not Found';
    }
}
