<?php
/**
 * Ice exception redirect class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Exception;

/**
 * Class Redirect
 *
 * Implements redirect exception
 *
 * @see Ice\Core\Exception
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Exception
 *
 * @version 0.1
 * @since   0.1
 */
class Http_Redirect extends Http
{
    private $redirectUrl = null;

    /**
     * Constructor for redirect exception
     *
     * @param string $errstr
     * @param array $errcontext
     * @param null $previous
     * @param null $errfile
     * @param null $errline
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.1
     * @throws FileNotFound
     */
    public function __construct($errstr, $errcontext = [], $previous = null, $errfile = null, $errline = null, $errno = 302)
    {
        $this->redirectUrl = $errstr;
        parent::__construct($errstr, $errcontext, $previous, $errfile, $errline, $errno);
    }

    /**
     * @return null
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function getHttpCode()
    {
        return 302;
    }

    public function getHttpMessage()
    {
        return 'Not Modified';
    }
}
