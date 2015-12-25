<?php

namespace Ice\Widget;

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
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'routeName' => ['providers' => 'router'],
                'routeParams' => ['providers' => 'router']
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
            ['route' => true, 'classes' => 'page-header', 'params' => $input['routeParams']]
        );
    }
}