<?php

namespace Ice\WidgetComponent;

use Ice\Exception\Not_Valid;

class Table_Row_Td extends HtmlTag
{
    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => __CLASS__, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    protected function getNotValidResult(Not_Valid $e)
    {
        return '<td>' . parent::getNotValidResult($e) . '</td>';
    }
}