<?php
/**
 * Ice core response class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
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
 * @package    Ice
 * @subpackage Core
 */
class Response
{
    /**
     * Response content
     *
     * @var string
     */
    private $content = '';

    /**
     * Content type
     *
     * @var string
     */
    private $contentType = null;

    /**
     * Http status code
     *
     * @var string
     */
    private $statusCode = null;

    /**
     * Redirect url
     *
     * @var string|null
     */
    private $redirectUrl = null;

    /**
     * @var bool
     */
    private $isError = false;

    /**
     * Private constructor of Request object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
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
     * @since   0.4
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
     * @since   0.0
     */
    public function send()
    {
        if (Request::isCli()) {
            fwrite($this->isError ? STDERR : STDOUT, $this->content);
            return;
        }


        if ($this->redirectUrl) {
            if (headers_sent()) {
                echo '<script type="text/javascript">location.href="' . $this->redirectUrl . '"</script>';
                return;
            }

            Http::setHeader('Location: ' . $this->redirectUrl, false, $this->statusCode);
            return;
        }

        if ($this->contentType) {
            Http::setHeader(Http::getContentTypeHeader($this->contentType), true, $this->statusCode);
        }

        if ($this->statusCode) {
            Http::setHeader(Http::getStatusCodeHeader($this->statusCode), true, $this->statusCode);
        }

        echo $this->content;
    }

    /**
     * Set response content
     *
     * @param string $content
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Set content type for response
     *
     * @param string $contentType
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
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
     * @since   0.4
     */
    public function setRedirectUrl($redirectUrl, $statusCode = 301)
    {
        $this->redirectUrl = $redirectUrl;
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
     * @since   0.4
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Set flag is error
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function setError()
    {
        $this->isError = true;
    }
}
