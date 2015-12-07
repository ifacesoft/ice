<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Widget;

class Render extends Action
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['template' => '', 'viewRenderClass' => 'Ice:Php', 'layout' => ''],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [
                'widget' => ['default' => null, 'providers' => ['default', 'request']],
                'widgets' => ['default' => [], 'providers' => ['default', 'request']]
            ],
            'output' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        $widgets = [];

        foreach ($input['widgets'] as $key => $widgetClass) {
            if (is_array($widgetClass)) {
                list($widgetClass, $params) = $widgetClass;
            } else {
                $params = [];
            }

            /** @var Widget $widgetClass */
            $widgetClass = Widget::getClass($widgetClass);

            if ($key == 'content') {
                $widgets['content'] = $widgetClass::getInstance($key, null, $params)->render();
            } else {
                $widget = $widgetClass::getInstance($key, null, $params);

                if (isset($input['widget'])) {
                    $widget->setResource(is_object($input['widget']) ? $input['widget']->getResource() : $input['widget']['resourceClass']);
                }

                $widgets[$widget->getWidgetId()] = $widget->render();
            }
        }

        return ['widgets' => $widgets];
    }
}