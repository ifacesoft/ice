<?php

namespace Ice\Widget;

class Admin_Nav extends Nav
{

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Nav::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
            'action' => [
                //  'class' => 'Ice:Render',
                //  'params' => [
                //      'widgets' => [
                ////        'Widget_id' => Widget::class
                //      ]
                //  ],
                //  'method' => 'POST'
            ]
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
    }
}