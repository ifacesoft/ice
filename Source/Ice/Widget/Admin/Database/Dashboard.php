<?php

namespace Ice\Widget;

use Ice\Core\Module;

class Admin_Database_Dashboard extends Nav
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
                //  'url' => true,
                //  'method' => 'POST',
                //  'callback' => null
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
        $module = Module::getInstance();

        $this->setClasses('nav-pills');

        foreach ($module->getDataSourceKeys() as $key => $dataSourceKey) {
            $this->li(
                'scheme_' . $key,
                [
                    'label' => 'scheme_' . $key,
                    'route' => 'ice_admin_database_database',
                    'params' => ['schemeName' => $key]
                ]
            );
        }
    }
}