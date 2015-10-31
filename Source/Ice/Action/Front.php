<?php

namespace Ice\Action;

use Ice\App;
use Ice\Core\Action;
use Ice\Core\Widget;
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
                'widgetClass' => ['providers' => 'router'],
                'widgetParams' => ['providers' => 'router'],
                'response' => ['providers' => 'router']
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

        if (isset($input['response'])) {
            if (isset($input['response']['contentType'])) {
                App::getResponse()->setContentType($input['response']['contentType']);
            }

            if (isset($input['response']['statusCode'])) {
                App::getResponse()->setStatusCode($input['response']['statusCode']);
            }
        }

        return ['content' => $widgetClass::getInstance(null, null, $widgetParams)];
    }
}