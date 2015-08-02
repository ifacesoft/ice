<?php

namespace Ice\Core;

abstract class Widget_Form_Security_Register extends Widget_Form
{
    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    abstract function register();
}
