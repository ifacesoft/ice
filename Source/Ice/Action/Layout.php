<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Widget;
use Ice\Core\Render as Core_Render;
use Ice\Widget\Resource_FooterJs;
use Ice\Widget\Resource_Static;
use Ice\Widget\Resource_Dynamic as Widget_Resource_Dynamic;

class Layout extends Action
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [
                'routeParams' => ['providers' => 'router', 'default' => []],
                'template' => '_Main',
                'renderClass' => 'Ice:Php',
                'widgets' => ['default' => ['title' => 'Ice:Title_Route', 'main' => 'Ice:Starter']]
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
        $routeParams = $input['routeParams'];
        unset($input['routeParams']);

        $routeParams['widgets'] = array_merge($input['widgets'], $routeParams['widgets']);

        $input = array_merge($input, $routeParams);

        $widgets = [
            'staticResources' => Resource_Static::getInstance(null),
            'dynamicResources' => Widget_Resource_Dynamic::getInstance(null),
            'footerJs' => Resource_FooterJs::getInstance(null)
        ];

        foreach ($input['widgets'] as $key => $widgetClass) {
            $widget = (array)$widgetClass;

            if (count($widget) == 2) {
                list($widgetClass, $widgetParams) = $widget;
            } else {
                $widgetClass = reset($widget);
                $widgetParams = [];
            }

            /** @var Widget $widgetClass */
            $widgetClass = Widget::getClass($widgetClass);
            $widgets[$key] = $widgetClass::getInstance(null, null, $widgetParams);
        }

        $template = $input['template'][0] == '_'
            ? Widget::getClass(__CLASS__ . $input['template'])
            : Widget::getClass($input['template']);

        /** @var Core_Render $renderClass */
        $renderClass = Core_Render::getClass($input['renderClass']);

        $widgets['dynamicResources']->addResource($template, 'js');
        $widgets['dynamicResources']->addResource($template, 'css');
        $widgets['dynamicResources']->addResource($template, 'less');

        return ['content' => $renderClass::getInstance()->fetch($template, $widgets)];
    }
}