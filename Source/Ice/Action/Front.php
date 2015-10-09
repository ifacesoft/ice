<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Widget;
use Ice\Core\Render as Core_Render;
use Ice\Widget\Layout;
use Ice\Widget\Resource_FooterJs;
use Ice\Widget\Resource_Static;
use Ice\Widget\Resource_Dynamic as Widget_Resource_Dynamic;

class Front extends Action
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
                'routeParams' => ['providers' => 'router'],
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
        /** @var Widget $widgetClass */
        $widgetClass = empty($input['routeParams']['widgetClass'])
            ? Layout::getClass()
            : Widget::getClass($input['routeParams']['widgetClass']);

        $widgetParams = empty($input['routeParams']['widgetParams'])
            ? []
            : $input['routeParams']['widgetParams'];

        return ['content' => $widgetClass::getInstance(null, null, $widgetParams)];
    }
}