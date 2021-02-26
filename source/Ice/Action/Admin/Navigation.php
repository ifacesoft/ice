<?php

namespace Ice\Action;

use Doctrine\Common\Util\Debug;
use Ice\Core\Action;
use Ice\Core\Module;
use Ice\Core\Request;
use Ice\Core\Route;
use Ice\Helper\Type_String;
use Ice\Widget\Menu\Navbar;

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
            'ttl' => -1,
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
            ->link(Route::getInstance(
                'ice_admin')->getUrl(),
                'Административная панель',
                ['active' => $currentUrl == $adminUrl ? true : false]
            );

        foreach ($input['items'] as $item) {
            if (isset($item['routeName'])) {
                $routeUrl = Route::getInstance($item['routeName'])->getUrl();
                $title = Route::getResource()->get($item['routeName']);

                $navbarMenu->link($routeUrl, $title, ['active' => Type_String::startsWith($currentUrl, $routeUrl) ? true : false]);
            } else {
                $navbarMenu->link($item['url'], $item['title'], ['active' => Type_String::startsWith($currentUrl, $item['url']) ? true : false]);
            }
        }

        return [
            'navbarMenu' => $navbarMenu
        ];
    }
}
