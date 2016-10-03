<?php
namespace Ice\Router;

use Ice\Core\Request;
use Ice\Exception\RouteNotFound;

class Symfony extends Ice
{
    public function getUrl($routeName = null)
    {
        if (!$routeName) {
            $routeName = $this->getName();
        }

        $routeName = (array)$routeName;

        $routeParams = [];
        $urlWithGet = false;
        $urlWithDomain = false;

        $url = null;

        try {
            if (count($routeName) == 4) {
                list($routeName, $routeParams, $urlWithGet, $urlWithDomain) = $routeName;
            } elseif (count($routeName) == 3) {
                list($routeName, $routeParams, $urlWithGet) = $routeName;
            } elseif (count($routeName) == 2) {
                list($routeName, $routeParams) = $routeName;
            } else {
                $routeName = reset($routeName);
            }

            $url = parent::getUrl([$routeName, $routeParams, $urlWithGet, $urlWithDomain]);
        } catch (RouteNotFound $e) {
            //
        }

        global $kernel;

        if (!$url) {
            $url = $kernel
                ->getContainer()
                ->get('router')
                ->generate($routeName, array_merge((array)$this->getParams(), $routeParams)); // todo: должно быть как строчкой ниже
//                ->generate($routeName, $routeParams);
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
        try {
            return parent::getName($url, $method);
        } catch (\Exception $e) {
            //
        }

        global $kernel;

        return $kernel->getContainer()->get('request')->get('_route');
    }

    /**
     * @return array
     */
    public function getParams()
    {
        try {
            return parent::getParams();
        } catch (\Exception $e) {
            //
        }

        global $kernel;

        return $kernel->getContainer()->get('request')->attributes->get('_route_params');
    }
}