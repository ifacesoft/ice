<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Module;
use Ice\Core\Request;
use Ice\Core\Route;
use Ice\Helper\String;
use Ice\Widget\Menu\Navbar;
use Ice\Core\Resource as Core_Resource;

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
                'items' => ['default' => []]
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
        $currentUrl = Request::uri();
        $adminUrl = Route::getInstance('ice_admin')->getUrl();

        $navbarMenu = Navbar::create(Route::getInstance('ice_admin')->getUrl(), __CLASS__)
            ->setBrand(Module::getInstance()->get('name'))
            ->setClasses('navbar-inverse navbar-fixed-top')
            ->link(
                'ice_admin',
                'Административная панель',
                [
                    'active' => $currentUrl == $adminUrl,
                    'route' => 'ice_admin'
                ]
            );

        foreach ($input['items'] as $item) {
            $navbarMenu->link(
                $item['routeName'],
                Core_Resource::create(Route::getClass())->get($item['routeName']),
                [
                    'active' => String::startsWith($currentUrl, Route::getInstance($item['routeName'])->getUrl()),
                    'route' => $item['routeName']
                ]
            );
        }

        return [
            'navbarMenu' => $navbarMenu
        ];
    }
}
