<?php

namespace Ice\Widget;

class Admin_Block extends Block_Render
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Block::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
//                'title' => ['default' => 'Ice:Title_Route'],
                'breadcrumbs' => 'Ice:Breadcrumbs_Route',
                'header' => 'Ice:Header_Route'
            ],
        ];
    }
}