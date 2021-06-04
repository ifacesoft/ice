<?php

namespace Ice\Router;

use Ice\Core\Exception;
use Ice\Core\Request;
use Ice\Core\Route;
use Ice\Core\Router;
use Ice\DataProvider\Router as DataProvider_Router;
use Ice\Exception\RouteNotFound;

class Ice extends Router
{
    /**
     * @param null $routeOptions
     * @param bool $force
     * @return null|string
     * @throws Exception
     * @throws RouteNotFound
     */
    public function getUrl($routeOptions = null, $force = false)
    {
        if (!$routeOptions) {
            $routeOptions = $this->getName();
        }

        list($routeName, $routeParams, $urlWithGet, $urlWithDomain, $replaceContext) = array_pad((array)$routeOptions, 5, false);

        $url = Route::getInstance($routeName)->getUrl((array)$routeParams);

        if (!$url) {
            return '';
        }

        if (!$urlWithGet) {
            $url = strtok($url, '?');
        }

        if (is_array($replaceContext)) {
            foreach ($replaceContext as $search => $replace) {
                $url = str_replace($search, $replace, $url);
            }
        }

        if ($urlWithDomain) {
            $domain = $urlWithDomain === true
                ? Request::protocol() . Request::host()
                : $urlWithDomain;

            return $domain . $url;
        }

        return $url;
    }

    public function getName($url = null, $method = null)
    {
        $provider = DataProvider_Router::getInstance();

        if (!$url) {
            $url = $provider->get('url', null, true);
        }

        if (!$method) {
            $method = $provider->get('method', null, true);
        }

        $route = $provider->getRoute($url, $method);

        return $route ? $route['routeName'] : null;
    }

    /**
     * @param bool $force
     * @return array
     * @throws Exception
     * @throws \Ice\Exception\Error
     */
    public function getParams($force = false)
    {
        $params = DataProvider_Router::getInstance()->get();

        return $params ? array_intersect_key($params['routeParams'], $params['params']) : [];
    }
}
