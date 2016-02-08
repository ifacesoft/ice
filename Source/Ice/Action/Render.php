<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Widget;
use Ice\DataProvider\Request;
use Ice\DataProvider\Router;
use Ice\Exception\Access_Denied;
use Ice\Exception\Http_Forbidden;

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
                'widgetClass' => ['providers' => ['default', Router::class, Request::class]],
                'widgetParams' => ['providers' => ['default', Router::class, Request::class], 'default' => []],
                'widget' => ['default' => null, 'providers' => ['default', Request::class]],
                'widgets' => ['default' => [], 'providers' => ['default', Request::class]]
            ],
            'output' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     * @throws Http_Forbidden
     */
    public function run(array $input)
    {
        $widgets = [];

        if (isset($input['widgetClass'])) {
            try {
                $widgetClass = Widget::getClass($input['widgetClass']);
                return ['content' => $widgetClass::getInstance(null, null, $input['widgetParams'])];
            } catch (Access_Denied $e) {
                throw new Http_Forbidden('В доступе отказано', [], $e);
            }
        }

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