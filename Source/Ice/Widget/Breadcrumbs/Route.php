<?php

namespace Ice\Widget;

use Ice\Core\Route;
use Ice\DataProvider\Router;

class Breadcrumbs_Route extends Breadcrumbs
{
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

    protected function build(array $input)
    {
        $result = parent::build($input);

        $this->setItems(Route::getInstance($input['routeName'])->getParentRoute(), $input['routeParams']);
        $this->li($input['routeName'], ['route' => [$input['routeName'], $input['routeParams']]]);

        return $result;
    }

    private function setItems($route, $params)
    {
        if ($route) {
            $this->setItems($route->getParentRoute(), $params);
            $this->item($route->getName(), ['route' => [$route->getName(), $params]]);
        }
    }
}