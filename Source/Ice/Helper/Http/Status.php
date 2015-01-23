<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 1/23/15
 * Time: 5:59 PM
 */

namespace Ice\Helper\Http;


class Status
{
    public static $headers = [
        '400' => 'HTTP/1.1 400 Bad Request',
        '403' => 'HTTP/1.1 403 Forbidden',
        '404' => 'HTTP/1.0 404 Not Found',
        '500' => 'HTTP/1.0 500 Internal Server Error'
    ];
}