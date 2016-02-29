<?php

namespace Ice\Router;

use Ice\Core\Route;
use Ice\Core\Router;
use Ice\DataProvider\Router as DataProvider_Router;

class Ice extends Router
{
    /**
     * @param null $routeName
     * @param array $params
     * @param bool $withGet
     * @return null|string
     * @throws \Ice\Exception\RouteNotFound
     */
    public function getUrl($routeName = null, array $params = [], $withGet = false)
    {
        $routeName = (array) $routeName;

        $routeParams = [];
        $urlWithGet = false;

        if (count($routeName) == 3) {
            list($routeName, $routeParams, $urlWithGet) = $routeName;
            $routeParams = array_merge($params, $routeParams);
        } elseif (count($routeName) == 2) {
            list($routeName, $routeParams) = $routeName;
            $routeParams = array_merge($params, $routeParams);
            $urlWithGet = $withGet;
        } else {
            $routeName = reset($routeName);
            $routeParams = $params;
            $urlWithGet = $withGet;
        }
        
        if ($url = Route::getInstance($routeName)->getUrl(array_merge($this->getParams(), $routeParams))) {
            return $url;
        }

        return null;
    }

    public function getName($url = null, $method = null)
    {
        if (!$url) {
            return Route::getInstance($this->getParams()['routeName']);
        }

        $route = DataProvider_Router::getInstance()->getRoute($url, $method);

        return $route ? $route['routeName'] : null;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return DataProvider_Router::getInstance()->get();
    }
}
