<?php

namespace Ice\Message;

use Ice\Core\Message;

class Mail extends Message
{
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => null, 'layout' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }
}