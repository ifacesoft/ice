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
use Ice\Helper\Json;

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
    private $statusCode = 200;

    private $content = null;

    private $error = null;

    private $success = null;

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
     * @param null $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @param null $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * Send data to standard output stream
     *
     * @param array $result
     *
     * @throws \Exception
     * @version 0.4
     * @since   0.0
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public function send(array $result)
    {
        $redirectUrl = isset($result['redirect']) ? $result['redirect'] : null;

        $isRedirect = $redirectUrl && in_array((int)$this->statusCode, [301, 302]);

        if ($isRedirect && !Request::isAjax()) {
            if (headers_sent()) {
                echo '<script type="text/javascript">location.href="' . $redirectUrl . '"</script>';
                return;
            }

            Http::setHeader('Location: ' . $redirectUrl, $this->statusCode);

            return;
        }

        if ($contentType = Request::getParam('contentType')) {
            $this->contentType = $contentType;
        }

        if ($this->content === null) {

            $this->content = $this->contentType === 'json' || Request::isAjax()
                ? str_replace(dirname(MODULE_DIR), '', Json::encode($result))
                : str_replace(dirname(MODULE_DIR), '', reset($result));
        }

        // Http::setHeader(Http::getContentLength(mb_strlen($this->content))); //todo: иногда отдает не правильно, хз почему

        if ($this->contentType) {
            Http::setHeader(Http::getContentTypeHeader($this->contentType));
        }

        if ($this->statusCode && (!Request::isAjax() || !$isRedirect)) {
            Http::setStatusCodeHeader($this->statusCode);
        }

        echo is_array($this->content) ? Json::encode($this->content) : $this->content;
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
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
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

        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return (int)$this->statusCode;
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
        $this->statusCode = (int)$statusCode;

        return $this;
    }

    /**
     * @return null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param null $content
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
