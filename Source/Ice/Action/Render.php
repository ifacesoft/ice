<?php

namespace Ice\Action;


use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\View;
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
                'widget' => [
                    'providers' => 'request',
                    'default' => null
                ],
                'widgets' => ['default' => []]
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

        Debuger::dump($input);

        foreach ($input['widgets'] as $key => $widgetClass) {
            /** @var Widget $widgetClass */
            $widgetClass = Widget::getClass($widgetClass);

            if ($key == 'content') {
                $widgets['content'] = $widgetClass::getInstance(null)->render();
            } else {
                $widget = $widgetClass::getInstance($key);

                if (isset($input['widget'])) {
                    $widget->setResource($input['widget']['resourceClass']);
                }

                $widgets[$widget->getId()] = $widget->render();
            }
        }

        return ['widgets' => $widgets];
    }
}