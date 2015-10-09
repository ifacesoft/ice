<?php

namespace Ice\Widget;

class Admin_Layout extends Block
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
            'resource' => ['js' => null, 'css' => true, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'title' => ['default' => 'Ice:Title_Route'],
                'staticResources' => ['default' => 'Ice:Resource_Static'],
                'footerJs' => ['default' => 'Ice:Resource_FooterJs'],
                'main' => ['default' => 'Ice:Admin_Database'],
                'navigation' => ['default' => 'Ice:Admin_Navigation'],
                'sidebar' => ['default' => 'Ice:Admin_Sidebar'],
            ],
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
       $this->widget('dynamicResources', ['widget' => Resource_Dynamic::getInstance(null)]);

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