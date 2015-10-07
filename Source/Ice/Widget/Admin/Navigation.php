<?php

namespace Ice\Widget;


use Ice\Core\Module;

class Admin_Navigation extends Navbar
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Navbar::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => ['routeNames' => ['default' => []]],
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
        $mainNav = Admin_Nav::getInstance('admin_main_nav')
            ->li('ice_admin_dashboard', ['route' => true]);

        foreach ($input['routeNames'] as $route) {
            $mainNav->li($route, ['route' => true]);
        }

        $profileNav = Admin_Nav::getInstance('admin_profile_nav')
            ->setClasses('navbar-right')
            ->li('ice_private', ['route' => true])
            ->li('ice_private_profile', ['route' => true]);

        $this
            ->brand('project_name', ['label' => Module::getInstance()->getName(), 'route' => 'ice_main'])
            ->nav('mainNav', ['widget' => $mainNav])
            ->nav('profileNav', ['widget' => $profileNav])
            ->setClasses('navbar-inverse navbar-fixed-top');
    }
}