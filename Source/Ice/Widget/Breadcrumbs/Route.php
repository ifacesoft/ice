<?php

namespace Ice\Widget;

use Ice\Core\Route;

class Breadcrumbs_Route extends Breadcrumbs
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Breadcrumbs::getClass(), 'class' => 'Ice:Php', 'layout' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [
                'routeName' => ['providers' => 'router']
            ],
            'output' => []
        ];
    }

    /**
     * Init widget parts and other
     * @param array $input
     * @return array|void
     */
    public function init(array $input)
    {
        $this->setItem(Route::getInstance($input['routeName'])->getParentRoute());
    }

    private function setItem($route)
    {
        if ($route) {
            $this->setItem($route->getParentRoute());
            $this->item($route->getName(), ['route' => true]);
        }
    }
}