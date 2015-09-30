<?php

namespace Ice\Widget;

use Ice\Core\Route;
use Ice\Core\Router;

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
            'render' => ['template' => Breadcrumbs::getClass(), 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => ['routeName' => ['providers' => 'router'], 'routeParams' => ['providers' => 'router']],
            'output' => []
        ];
    }

    /**
     * @param string $key
     * @param null $ttl
     * @param array $params
     * @return Breadcrumbs_Route
     */
    public static function getInstance($key, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
    }

    protected function build(array $input)
    {
        $result = parent::build($input);

        $this->setItems(Route::getInstance($input['routeName'])->getParentRoute());
        $this->li($input['routeName'], ['route' => true, 'params' => $input['routeParams']]);


        return $result;
    }

    private function setItems($route)
    {
        if ($route) {
            $this->setItems($route->getParentRoute());
            $this->item($route->getName(), ['route' => true]);
        }
    }
}