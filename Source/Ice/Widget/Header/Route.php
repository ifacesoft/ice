<?php

namespace Ice\Widget;

use Ice\DataProvider\Router;

class Header_Route extends Header
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => Header::class, 'class' => 'Ice:Php', 'layout' => null, 'resource' => Header::class],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Access denied'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'routeName' => ['providers' => Router::class],
                'routeParams' => ['providers' => Router::class]
            ],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        $this->h1(
            $input['routeName'],
            ['route' => ['name' => $input['routeName'], 'params' => $input['routeParams']], 'classes' => 'page-header']
        );
    }
}