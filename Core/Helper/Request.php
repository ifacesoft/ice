<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 22.01.14
 * Time: 22:52
 */

namespace ice\core\helper;


use ice\core\Data_Provider;

class Request
{
    const DEFAULT_DATA_PROVIDER_KEY = 'Request:http/';

    public static function __callStatic($name, array $arguments)
    {
        return Data_Provider::getInstance(self::DEFAULT_DATA_PROVIDER_KEY)->get($name);
    }
}