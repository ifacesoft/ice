<?php

namespace Ice\Helper;

use Ice\Core\Request as Core_Request;

class Http
{
//    /**
//     * Headers by http response code
//     *
//     * @var array
//     */
//    private static $statusCodeHeaders = [
//        200 => 'HTTP/1.1 200 OK', // Успешно
//        201 => 'HTTP/1.1 201 Created', // Создано
//        202 => 'HTTP/1.1 202 Accepted', // Принято
//        400 => 'HTTP/1.1 400 Bad Request', // Плохой, неверный запрос
//        401 => 'HTTP/1.1 401 Unauthorized', // Не авторизован
//        403 => 'HTTP/1.1 403 Forbidden', // В доступе отказано
//        404 => 'HTTP/1.1 404 Not Found', // Не найдено
//        405 => 'HTTP/1.1 405 Method Not Allowed', // Метод не поддерживается
//        409 => 'HTTP/1.1 409 Conflict', // Конфликт
//        412 => 'HTTP/1.1 412 Precondition Failed', // Условие ложно
//        413 => 'HTTP/1.1 413 Request Entity Too Large', // Размер запроса слижком велик
//        500 => 'HTTP/1.1 500 Internal Server Error', // Внутренняя ошибка сервера
//        501 => 'HTTP/1.1 501 Not Implemented', // Не реализовано
//        503 => 'HTTP/1.1 502 Service Unavailable' // Сервис не доступен
//    ];

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
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'docx' => ' application/vnd.openxmlformats-officedocument.wordprocessingml.document',

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
        $mimeType = isset(Http::$mimeTypes[$extension]) ? Http::$mimeTypes[$extension] : $extension;

        return 'Content-Type: ' . $mimeType . '; charset=utf-8';
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

//    /**
//     * Return status code header by code
//     *
//     * @param  $code
//     * @return mixed
//     *
//     * @author dp <denis.a.shestakov@gmail.com>
//     *
//     * @package    Ice
//     * @subpackage Helper
//     *
//     * @version 0.4
//     * @since   0.4
//     */
//    public static function getStatusCodeHeader($code)
//    {
//        return Http::$statusCodeHeaders[$code];
//    }

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

    public static function setStatusCodeHeader($code)
    {
        if (!headers_sent()) {
            http_response_code($code);
        }
//        Http::setHeader(Http::getStatusCodeHeader($code), $code);
    }
}
