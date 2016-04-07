<?php
namespace Ice\Router;

use Ice\Core\Debuger;
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
            $url = $kernel->getContainer()->get('router')->generate($routeName, array_merge($this->getParams(), $routeParams));
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
        $routeName = null;

        try {
            $routeName = parent::getName($url, $method);
        } catch (\Exception $e) {
           //
        }

        if (!$routeName) {
            global $kernel;

            try {
                $routeName = $kernel->getContainer()->get('request')->get('_route');
            } catch (\Exception $e) {
                //
            }
        }

        return $routeName;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        global $kernel;

        return $kernel->getContainer()->get('request')->attributes->get('_route_params');
    }
}