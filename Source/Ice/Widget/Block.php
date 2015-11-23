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
            'render' => ['template' => null, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
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
        foreach ($input as $name => $widgetClass) {
            $widgetClass = (array)$widgetClass;

            if (count($widgetClass) == 3) {
                list($widgetClass, $widgetParams, $instanceKey) = $widgetClass;
            } else if (count($widgetClass) == 2) {
                list($widgetClass, $widgetParams) = $widgetClass;
                $instanceKey = null;
            } else {
                $widgetClass = reset($widgetClass);
                $widgetParams = [];
                $instanceKey = null;
            }

            $this->widget($name, ['widget' => [$widgetClass, $widgetParams, $instanceKey]]);
        }
    }
}