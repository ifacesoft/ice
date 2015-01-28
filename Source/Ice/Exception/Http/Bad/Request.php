<?php
/**
 * Ice exception http bad request (code 400) class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Exception;

use Ice\Core\Exception;

/**
 * Class Http_Bad_Request
 *
 * Implements http bad request exception
 *
 * @see Ice\Core\Exception
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Exception
 */
class Http_Bad_Request extends Exception
{
    /**
     * Constructor for http bad request exception
     *
     * @param string $errstr
     * @param array $errcontext
     * @param null $previous
     * @param null $errfile
     * @param null $errline
     * @param int $errno
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function __construct($errstr, $errcontext = [], $previous = null, $errfile = null, $errline = null, $errno = 0)
    {
        parent::__construct($errstr, $errcontext, $previous, $errfile, $errline, E_USER_ERROR);
    }
} 