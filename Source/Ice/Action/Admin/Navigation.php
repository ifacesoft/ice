<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Module;
use Ice\Core\Request;
use Ice\Core\Route;
use Ice\Helper\String;
use Ice\Core\Resource as Core_Resource;
use Ice\Widget\Form;
use Ice\Widget\Nav;
use Ice\Widget\Navbar;

class Admin_Navigation extends Action
{

    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Php'],
            'input' => [
                'routeNames' => ['default' => []]
            ],
            'output' => [],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'roles' => []
        ];
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function run(array $input)
    {
        $mainNav = Nav::create()
            ->li('ice_admin_dashboard', ['route' => true]);

        foreach ($input['routeNames'] as $route) {
            $mainNav->li($route, ['route' => true]);
        }

        $profileNav = Nav::create()
            ->setClasses('navbar-right')
            ->li('ice_private', ['route' => true])
            ->li('ice_private_profile', ['route' => true]);

        $navbar = Navbar::create()
            ->brand('project_name', ['label' => Module::getInstance()->getName(), 'href' => '/'])
            ->nav('main', ['widget' => $mainNav])
            ->nav('profile', ['widget' => $profileNav])
            ->setClasses('navbar-inverse navbar-fixed-top');

        return [
            'navbar' => $navbar
        ];
    }
}
