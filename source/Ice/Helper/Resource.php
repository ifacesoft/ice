<?php

namespace Ice\Helper;

use Ice\Core\Render;
use Ice\Render\Replace;

class Resource
{
    public static function getMessage($message)
    {
        if (is_string($message)) {
            return $message;
        }

        list($message, $params) = $message;

        return str_replace(
            ["\t", "\n"],
            ' ',
            Replace::getInstance()->fetch($message, (array)$params, null, Render::TEMPLATE_TYPE_STRING)
        );
    }
}
