<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 21.01.16
 * Time: 18:42
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Widget;
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
                'class' => ['providers' => ['default', 'router', 'request']],
                'params' => ['providers' => ['default', 'router', 'request'], 'default' => []],
                'widget' => ['default' => null, 'providers' => ['default', 'request']]
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
        $widgetClass = Widget::getClass($input['class']);

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="report.xlsx"',
            'Cache-Control' => 'max-age=0',
        ];

        foreach ($headers as $type => $header) {
            header($type . ': ' . $header);
        }

        return [
            'content' => External_PHPExcel::getInstance()
                ->renderWidget($widgetClass::getInstance(null, null, $input['params']))
        ];
    }
}