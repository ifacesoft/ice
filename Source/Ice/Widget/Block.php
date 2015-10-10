<?php

namespace Ice\Widget;

use Ice\Core\Widget;

class Block extends Widget
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
            'action' => [
                //  'class' => 'Ice:Render',
                //  'params' => [
                //      'widgets' => [
                ////        'Widget_id' => Widget::class
                //      ]
                //  ],
                //  'url' => true,
                //  'method' => 'POST',
                //  'callback' => null
            ]
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        foreach ($input as $name => $widgetClass) {
            $widgetClass = (array) $widgetClass;

            if (count($widgetClass) == 2) {
                list($widgetClass, $widgetParams) = $widgetClass;
            } else {
                $widgetClass = reset($widgetClass);
                $widgetParams = [];
            }

            $this->widget($name, ['widget' => $widgetClass, 'params' => $widgetParams]);
        }
    }
}