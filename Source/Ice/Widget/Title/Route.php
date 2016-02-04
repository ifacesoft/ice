<?php

namespace Ice\Widget;

use Ice\Core\Widget;
use Ice\Data\Provider\Router;

class Title_Route extends Widget
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => '', 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
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
        $this->text($input['routeName'], ['route' => true, 'params' => $input['routeParams']]);
    }
}