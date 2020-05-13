<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Route;

class Cache_Hit extends Action
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['template' => '', 'viewRenderClass' => null],
            'actions' => [],
            'input' => ['routeNames' => ['default' => []]],
            'output' => [],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'access' => [
                'roles' => [],
                'request' => 'cli',
                'env' => null
            ]
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        foreach ($input['routeNames'] as $routeName => $params) {
            if (is_int($routeName)) {
                $routeName = $params;
                $params = [];
            }

            $route = Route::getInstance($routeName);

            $_SERVER['REQUEST_URI'] = $route->getUrl($params);

            $requestGet = $route->gets('request/GET');

            /** @var Action $action */
            list($action, $params) = each($requestGet);

            $action::call($params);
        }
    }
}