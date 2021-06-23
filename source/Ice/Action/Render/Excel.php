<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Exception;
use Ice\Core\Widget;
use Ice\DataProvider\Request;
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
                'class' => ['providers' => ['default', Request::class]],
            ],
            'output' => []
        ];
    }

    /** Run action
     *
     * @param array $input
     * @return array
     * @throws Exception
     */
    public function run(array $input)
    {
        /** @var Widget $widgetClass */
        $widgetClass = Widget::getClass($input['class']);

        /** @var Widget $widget */
        $widget = $widgetClass::getInstance(null, null, Request::getInstance()->get());

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
