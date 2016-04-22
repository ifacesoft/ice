<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 19.04.16
 * Time: 11:27
 */

namespace Ice\WidgetComponent;

use Ice\Core\Module;
use Ice\Helper\Date;
use Ice\Helper\String;

class ValueElement extends HtmlTag
{
    private $value = null;

    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    
}