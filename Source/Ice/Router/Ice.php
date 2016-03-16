<?php

namespace Ice\Router;

use Ice\Core\Request;
use Ice\Core\Route;
use Ice\Core\Router;
use Ice\DataProvider\Router as DataProvider_Router;

class Ice extends Router
{
    /**
     * @param null $routeName
     * @return null|string
     * @throws \Ice\Exception\RouteNotFound
     */
    public function getUrl($routeName = null)
    {
        $routeName = (array) $routeName;

        $routeParams = [];
        $urlWithGet = false;
        $withDomain = false;

        if (count($routeName) == 4) {
            list($routeName, $routeParams, $urlWithGet, $withDomain) = $routeName;
        } elseif (count($routeName) == 3) {
            list($routeName, $routeParams, $urlWithGet) = $routeName;
        } elseif (count($routeName) == 2) {
            list($routeName, $routeParams) = $routeName;
        } else {
            $routeName = reset($routeName);
        }
        
        $url = Route::getInstance($routeName)->getUrl(array_merge($this->getParams(), $routeParams));

        if (!$url) {
            return '';
        }

        if (!$urlWithGet) {
            $url = strtok($url,'?');
        }

        if ($withDomain) {
            return Request::protocol() . Request::host() . $url;
        }

        return $url;
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
