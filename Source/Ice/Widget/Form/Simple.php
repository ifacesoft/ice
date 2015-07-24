<?php

namespace Ice\Widget\Form;

use Ice\Core\Debuger;
use Ice\Core\Widget_Form;
use Ice\Helper\Emmet;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\View\Render\Php;

class Simple extends Widget_Form
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
