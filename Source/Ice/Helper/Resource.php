<?php

namespace Ice\Helper;

use Ice\Core\View_Render;
use Ice\View\Render\Replace;

class Resource
{
    public static function getMessage($message)
    {
        if (is_string($message)) {
            return $message;
        }

        list($message, $params) = $message;

        return str_replace(["\t", "\n"], ' ', Replace::getInstance()->fetch($message, (array)$params, View_Render::TEMPLATE_TYPE_STRING));
    }
}