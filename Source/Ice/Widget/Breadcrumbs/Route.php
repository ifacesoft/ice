<?php

namespace Ice\Widget;

use Ice\Core\Route;
use Ice\DataProvider\Router;

class Breadcrumbs_Route extends Breadcrumbs
{
    /**
     * @param string $instanceKey
     * @param null $ttl
     * @param array $params
     * @return Breadcrumbs_Route
     */
    public static function getInstance($instanceKey, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

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
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'routeName' => ['providers' => Router::class],
                'routeParams' => ['providers' => Router::class]
            ],
            'output' => [],
        ];
    }

    protected function build(array $input)
    {
        $result = parent::build($input);

        $this->setItems(Route::getInstance($input['routeName'])->getParentRoute(), $input['routeParams']);
        $this->li($input['routeName'], ['route' => ['name' => $input['routeName'], 'params' => $input['routeParams']]]);

        return $result;
    }

    private function setItems($route, $params)
    {
        if ($route) {
            $this->setItems($route->getParentRoute(), $params);
            $this->item($route->getName(), ['route' => ['name' => $route->getName(), 'params' => $params]]);
        }
    }
}