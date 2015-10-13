<?php

namespace Ice\Widget;

use Ice\Core\Debuger;
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
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'routeName' => ['providers' => 'router'],
                'routeParams' => ['providers' => 'router']
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

        $this->setItems(Route::getInstance($input['routeName'])->getParentRoute(), $input['routeParams']);
        $this->li($input['routeName'], ['route' => true, 'params' => $input['routeParams']]);

        return $result;
    }

    private function setItems($route, $params)
    {
        if ($route) {
            $this->setItems($route->getParentRoute(), $params);
            $this->item($route->getName(), ['route' => true, 'params' => $params]);
        }
    }
}