<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Route;
use Ice\Widget\Header;
use Ice\Widget\Nav;

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
        $routeName = $this->getRouteName($input['routeNames'], $input['routeName']);

        if (!$routeName) {
            $routeName = $this->getRouteName($input['routeNames'], substr($input['routeName'], 0, strrpos($input['routeName'], '_')));
        }

        return ['nav' => $this->getNav($input['routeNames'][$routeName])];
    }

    private function getRouteName(array $routeNames, $currentRouteName)
    {
        foreach ($routeNames as $key => $routeName) {
            if ($routeName == $currentRouteName) {
                return $key;
            }

            if (is_array($routeName) && $this->getRouteName($routeName, $currentRouteName) !== null) {
                return $key;
            }
        }

        return null;
    }

    private function getNav(array $routeNames, $routeName = null)
    {
        $nav = Nav::create();

        if ($routeName) {
            $nav->widget($routeName, ['widget' => Header::create()->h4($routeName, ['route' => true])]);
        }

        foreach ($routeNames as $key => $routeName) {
            if (is_array($routeName)) {
                $nav->widget($key, ['widget' => $this->getNav($routeName, $key)]);
            } else {
                $nav->li($routeName, ['route' => true]);
            }
        }

        return $nav;
    }
}