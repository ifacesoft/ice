<?php

namespace Ice\Widget;

use Ice\Action\Render;
use Ice\Core\Widget;

class Table extends Widget
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => __CLASS__, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
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
        return [];
    }

    public function getPagination($widgetClass = 'Ice\Widget\Pagination')
    {
        return $this->getWidget($widgetClass)
            ->setEvent([
                'action' => Render::class,
                'params' => [
                    'widgets' => [
                        $this->getInstanceKey() => get_class($this)
                    ]
                ],
//                'method' => 'GET'
            ]);
    }
}