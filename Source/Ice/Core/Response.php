<?php
/**
 * Ice core response class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */
namespace Ice\Core;

use Ice\Helper\Http;

/**
 * Class Response
 *
 * Core response class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 */
class Response
{
    /**
     * Response content
     *
     * @var string
     */
    private $_content = '';

    /**
     * Content type
     *
     * @var string
     */
    private $_contentType = null;

    /**
     * Http status code
     *
     * @var string
     */
    private $_statusCode = null;

    /**
     * Redirect url
     *
     * @var string|null
     */
    private $_redirectUrl = null;

    /**
     * @var bool
     */
    private $_isError = false;

    /**
     * Private constructor of Request object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    private function __construct()
    {
    }

    /**
     * Create instance of Response
     *
     * @return Response
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public static function create()
    {
        return new Response();
    }

    /**
     * Send data to standard output stream
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function send()
    {
        if (Request::isCli()) {
            fwrite($this->_isError ? STDERR : STDOUT, $this->_content);
            return;
        }


        if ($this->_redirectUrl) {
            if (headers_sent()) {
                echo '<script type="text/javascript">location.href="' . $this->_redirectUrl . '"</script>';
                return;
            }

            header('Location: ' . $this->_redirectUrl, false, $this->_statusCode);
            return;
        }

        if ($this->_contentType) {
            header(Http::getContentTypeHeader($this->_contentType), true, $this->_statusCode);
        }

        if ($this->_statusCode) {
            header(Http::getStatusCodeHeader($this->_statusCode), true, $this->_statusCode);
        }

        echo $this->_content;
    }

    /**
     * Set response content
     *
     * @param string $content
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

    /**
     * Set content type for response
     *
     * @param string $contentType
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function setContentType($contentType)
    {
        $this->_contentType = $contentType;
    }

    /**
     * Set redirect url
     *
     * @param null $redirectUrl
     * @param int $statusCode
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function setRedirectUrl($redirectUrl, $statusCode = 301)
    {
        $this->_redirectUrl = $redirectUrl;
        $this->setStatusCode($statusCode);
    }

    /**
     * Set status code for response
     *
     * @param string $statusCode
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function setStatusCode($statusCode)
    {
        $this->_statusCode = $statusCode;
    }

    /**
     * Set flag is error
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function setError()
    {
        $this->_isError = true;
    }
}