<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Widget\Admin_Sidebar as Widget_Admin_Sidebar;

class Admin_Sidebar extends Action
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Php', 'layout' => null],
            'actions' => [],
            'input' => [
                'routeNames' => ['default' => []],
                'routeName' => ['providers' => 'router']
            ],
            'output' => [],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        return ['nav' => Widget_Admin_Sidebar::getInstance(null)];
    }
}