<?php
/**
 * Ice exception file not found class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Exception;

use Ice\Core\Exception;

/**
 * Class FileNotFound
 *
 * Implements file not found exception
 *
 * @see Ice\Core\Exception
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Exception
 */
class FileNotFound extends Exception
{
    /**
     * Constructor for file not found exception
     *
     * @param string $errstr
     * @param array $errcontext
     * @param null $previous
     * @param null $errfile
     * @param null $errline
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public function __construct($errstr, $errcontext = [], $previous = null, $errfile = null, $errline = null)
    {
        parent::__construct($errstr, $errcontext, $previous, $errfile, $errline, E_USER_ERROR);
    }
}
