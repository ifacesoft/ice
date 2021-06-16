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
use Ice\Core\Environment;
use Ice\Core\Exception;
use Ice\Exception\Config_Error;
use Ice\Exception\FileNotFound;
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
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => ''],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'status' => ['default' => 'Error'],
                'code' => ['default' => 500],
                'message' => ['default' => ''],
                'stackTrace' => ['default' => '']
            ],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     */
    protected function build(array $input)
    {
        Http::setStatusCodeHeader($input['code']);

//        $this->setTemplateClass('_' . $input['code']);

        $stackTrace = Environment::getInstance()->isProduction()
            ? '' :
            str_replace(': ', ': ' . "\n\t" . '<span style="color:gray;">', $input['stackTrace']);

        $stackTrace = str_replace("\n" . '#', '</span>' . "\n" . '#', $stackTrace);

        return [
            'message' => strip_tags($input['message']),
            'stackTrace' => $stackTrace,
            'code' => $input['code'],
            'status' => $input['status']
        ];
    }
}
