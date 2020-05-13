<?php
namespace Ice\Router;

use Ice\Core\Request;
use Ice\Exception\RouteNotFound;

class Symfony extends Ice
{
    public function getUrl($routeName = null, $force = false)
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

            if (!$force) {
                return parent::getUrl([$routeName, $routeParams, $urlWithGet, $urlWithDomain]);
            }

        } catch (RouteNotFound $e) {
            //
        }

        global $kernel;

        if (!$url) {
            $url = $kernel
                ->getContainer()
                ->get('router')
                ->generate($routeName, array_merge((array)$this->getParams($force), $routeParams)); // todo: должно быть как строчкой ниже
//                ->generate($routeName, $routeParams);
        }

        if (!$urlWithGet) {
            $url = strtok($url, '?');
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
        try {
            return parent::getName($url, $method);
        } catch (\Exception $e) {
            //
        }

        global $kernel;

        return $kernel->getContainer()->get('request_stack')->getCurrentRequest()->get('_route');
    }

    /**
     * @param bool $force
     * @return array
     */
    public function getParams($force = false)
    {
        if (!$force) {
            try {
                return parent::getParams();
            } catch (\Exception $e) {
                //
            }
        }

        global $kernel;

        return $kernel->getContainer()->get('request_stack')->getCurrentRequest()->attributes->get('_route_params');
    }
}