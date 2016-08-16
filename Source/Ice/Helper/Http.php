<?php
namespace Ice\Helper;

use Ice\Core\Request as Core_Request;

class Http
{
    /**
     * Headers by http response code
     *
     * @var array
     */
    private static $statusCodeHeaders = [
        '400' => 'HTTP/1.1 400 Bad Request',
        '401' => 'HTTP/1.1 401 Unauthorized',
        '403' => 'HTTP/1.1 403 Forbidden',
        '404' => 'HTTP/1.1 404 Not Found',
        '500' => 'HTTP/1.1 500 Internal Server Error'
    ];

    /**
     * Mime types by file extensions
     *
     * @var array
     */
    private static $mimeTypes = [
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    ];

    /**
     * Return content type header by file extension
     *
     * @param  $extension
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @package    Ice
     * @subpackage Helper
     *
     * @version 0.4
     * @since   0.4
     */
    public static function getContentTypeHeader($extension)
    {
        return 'Content-Type: ' . Http::$mimeTypes[$extension] . '; charset=utf-8';
    }

    /**
     * Return content type header by file extension
     *
     * @param  $length
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @package    Ice
     * @subpackage Helper
     *
     * @version 1.1
     * @since   1.1
     */
    public static function getContentLength($length)
    {
        return 'Content-Length: ' . $length;
    }

    /**
     * Return status code header by code
     *
     * @param  $code
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @package    Ice
     * @subpackage Helper
     *
     * @version 0.4
     * @since   0.4
     */
    public static function getStatusCodeHeader($code)
    {
        return Http::$statusCodeHeaders[$code];
    }

    /**
     * Gets content via http
     *
     * @param  $url
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @package    Ice
     * @subpackage Helper
     *
     * @version 0.4
     * @since   0.4
     */
    public static function getContents($url)
    {
        if (Core_Request::isCli()) {
            fwrite(STDOUT, Console::getText($url, Console::C_GREEN_B) . "\n");
        }

        return file_get_contents($url);
    }

    public static function setHeader($header, $code = null, $replace = true)
    {
        if (!headers_sent()) {
            header($header, $replace, $code);
        }
    }
}
