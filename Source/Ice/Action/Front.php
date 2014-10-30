<?php
/**
 * Ice action front class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice;
use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Model;
use Ice\Core\Response;
use Ice\Core\Route;
use Ice\Data\Provider\Router;

/**
 * Class Front
 *
 * Standard front action
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Action
 *
 * @version stable_0
 * @since stable_0
 */
class Front extends Action
{
    /**  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standart|file
     *      'viewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'layout' => '',
        'viewRenderClassName' => 'Ice:Php',
        'inputDataProviderKeys' => [Router::DEFAULT_KEY],
    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        $route = Route::getInstance($input['routeName']);

        $data = $route->getData();

        $methodData = $data[$input['method']];

        if (isset($methodData['redirect'])) {
            if (isset($input['redirectUrl'])) {
                Response::redirect($input['redirectUrl']);
            } else {
                Response::redirect(Route::getUrl($data[$input['method']]['redirect']));
            }
        }

        if (!isset($methodData['layout'])) {
            $methodData['layout'] = Action::getConfig()->get('layoutActionName');
        }

        $actionContext->addAction($methodData['layout'], ['actions' => $methodData['actions']], 'layout');
    }
}