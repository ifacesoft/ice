<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Widget;
use Ice\Data\Provider\Router;
use Ice\Widget\Layout;

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
                'widgetClass' => ['providers' => Router::class],
                'widgetParams' => ['providers' => Router::class],
                'response' => ['providers' => Router::class]
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
        $widgetClass = empty($input['widgetClass'])
            ? Layout::getClass()
            : Widget::getClass($input['widgetClass']);

        $widgetParams = (array)$input['widgetParams'];



        return ['content' => $widgetClass::getInstance(null, null, $widgetParams)];
    }
}