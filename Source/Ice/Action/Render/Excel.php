<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 21.01.16
 * Time: 18:42
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Widget;
use Ice\DataProvider\Request;
use Ice\DataProvider\Router;
use Ice\Render\External_PHPExcel;

class Render_Excel extends Action
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
                'class' => ['providers' => ['default', Router::class, Request::class]],
                'params' => ['providers' => ['default', Router::class, Request::class], 'default' => []],
                'widget' => ['default' => null, 'providers' => ['default', Request::class]]
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
        $widgetClass = Widget::getClass($input['class']);

        /** @var Widget $widget */
        $widget = $widgetClass::getInstance(null, null, $input['params']);

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $widget->getCanonicalName() . '.xlsx"',
            'Cache-Control' => 'max-age=0',
        ];

        foreach ($headers as $type => $header) {
            header($type . ': ' . $header);
        }

        return [
            'content' => External_PHPExcel::getInstance()->renderWidget($widget)
        ];
    }
}