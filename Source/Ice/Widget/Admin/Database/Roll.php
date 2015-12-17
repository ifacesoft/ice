<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 17.12.15
 * Time: 14:16
 */

namespace Ice\Widget;

class Admin_Database_Roll extends Block_Render
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => __CLASS__, 'class' => 'Ice:Php', 'layout' => null, 'resource' => Admin_Database_Database::getClass()],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'table' => ['default' => Admin_Database_Table::getClass()],
                'filter' => ['default' => [Admin_Database_Form::getClass(), ['mode' => 'filter']]]
            ],
            'output' => []
        ];
    }
}