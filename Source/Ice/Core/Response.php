<?php
/**
 * Ice core response class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */
namespace Ice\Core;

/**
 * Class Response
 *
 * Core response class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
class Response
{
    /**
     * Redirecting to uri
     *
     * @param $uri
     * @param int $code
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function redirect($uri, $code = 301)
    {
        if (headers_sent()) {
            echo '<script type="text/javascript">location.href="' . $uri . '"</script>';
            exit;
        }

        header('Location: ' . $uri, false, $code);
        exit;
    }

    /**
     * Send data to standard output stream
     *
     * @param $output
     * @param bool $isError
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function send($output, $isError = false)
    {
        if (Request::isCli()) {
            fwrite($isError ? STDERR : STDOUT, $output);
            return;
        }

        echo $output instanceof View ? $output->getContent() : $output;
    }
}