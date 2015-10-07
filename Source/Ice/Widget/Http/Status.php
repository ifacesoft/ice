<?php
/**
 * Ice action http status 400 class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Widget;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Helper\Http;

/**
 * Class Http_Status_400
 *
 * Action for page with status 400
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since   0.0
 */
class Http_Status extends Block
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'code' => ['default' => 500],
                'message' => ['default' => ''],
                'stackTrace' => ['default' => '']
            ],
            'output' => [],
            'action' => [
                //  'class' => 'Ice:Render',
                //  'params' => [
                //      'widgets' => [
                ////        'Widget_id' => Widget::class
                //      ]
                //  ],
                //  'url' => true,
                //  'method' => 'POST',
                //  'callback' => null
            ]
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        Http::setHeader(Http::getStatusCodeHeader($input['code']), true, $input['code']);

        $this->setTemplate('_' . $input['code']);

        return ['message' => strip_tags($input['message'])];
    }
}
