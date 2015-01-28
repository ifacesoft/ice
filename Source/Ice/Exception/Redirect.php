<?php
/**
 * Ice exception redirect class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Exception;

use Ice\Core\Exception;

/**
 * Class Redirect
 *
 * Implements redirect exception
 *
 * @see Ice\Core\Exception
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Exception
 *
 * @version 0.1
 * @since 0.1
 */
class Redirect extends Exception
{
    /**
     * Constructor for redirect exception
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
     * @version 0.1
     * @since 0.1
     */
    public function __construct($errstr, $errcontext = [], $previous = null, $errfile = null, $errline = null, $errno = -1)
    {
        parent::__construct($errstr, $errcontext, $previous, $errfile, $errline, E_USER_ERROR);
    }
}