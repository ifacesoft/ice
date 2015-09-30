<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Module;
use Ice\Core\Request;
use Ice\Core\Route;
use Ice\Helper\String;
use Ice\Core\Resource as Core_Resource;
use Ice\Widget\Admin_Nav;
use Ice\Widget\Admin_Navbar;
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
        $mainNav = Admin_Nav::getInstance('admin_main_nav')
            ->li('ice_admin_dashboard', ['route' => true]);

        foreach ($input['routeNames'] as $route) {
            $mainNav->li($route, ['route' => true]);
        }

        $profileNav = Admin_Nav::getInstance('admin_profile_nav')
            ->setClasses('navbar-right')
            ->li('ice_private', ['route' => true])
            ->li('ice_private_profile', ['route' => true]);

        $navbar = Admin_Navbar::getInstance('admin_navbar')
            ->nav('mainNav', ['widget' => $mainNav])
            ->nav('profileNav', ['widget' => $profileNav])
            ->setClasses('navbar-inverse navbar-fixed-top');

        return [
            'navbar' => $navbar
        ];
    }
}
