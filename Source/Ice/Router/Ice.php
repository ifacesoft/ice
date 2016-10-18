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
     * @param bool $force
     * @return null|string
     */
    public function getUrl($routeName = null, $force = false)
    {
        if (!$routeName) {
            $routeName = $this->getName();
        }

        $routeName = (array)$routeName;

        $routeParams = [];
        $urlWithGet = false;
        $urlWithDomain = false;

        if (count($routeName) == 4) {
            list($routeName, $routeParams, $urlWithGet, $urlWithDomain) = $routeName;
        } elseif (count($routeName) == 3) {
            list($routeName, $routeParams, $urlWithGet) = $routeName;
        } elseif (count($routeName) == 2) {
            list($routeName, $routeParams) = $routeName;
        } else {
            $routeName = reset($routeName);
        }

        $url = Route::getInstance($routeName)->getUrl((array)$routeParams);

        if (!$url) {
            return '';
        }

        if (!$urlWithGet) {
            $url = strtok($url, '?');
        }

        if ($urlWithDomain) {
            return Request::protocol() . Request::host() . $url;
        }

        return $url;
    }

    public function getName($url = null, $method = null)
    {
        $provider = DataProvider_Router::getInstance();

        if (!$url) {
            $url = $provider->get('url');
        }

        if (!$method) {
            $method = $provider->get('method');
        }

        $route = $provider->getRoute($url, $method);

        return $route ? $route['routeName'] : null;
    }

    /**
     * @param bool $force
     * @return array
     */
    public function getParams($force = false)
    {
        $params = DataProvider_Router::getInstance()->get();

        return array_intersect_key($params['routeParams'], $params['params']);
    }
}
