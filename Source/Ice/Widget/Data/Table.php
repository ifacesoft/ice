<?php

namespace Ice\Widget\Data;

use Ice\Core\Widget_Data;

class Table extends Widget_Data
{
    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }
}
