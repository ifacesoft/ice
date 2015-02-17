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
use Ice\Core\Route;
use Ice\Data\Provider\Router;
use Ice\Exception\Redirect;

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
 * @version 0.0
 * @since 0.0
 */
class Front extends Action
{
    /**  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standard|file
     *      'defaultViewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'view' => [
            'layout' => '',
            'viewRenderClass' => 'Ice:Php'
        ],
        'input' => [
            Router::DEFAULT_DATA_PROVIDER_KEY => ['routeName', 'method']
        ]
    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     * @throws Redirect
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        $route = Route::getInstance($input['routeName']);

        $redirectUrl = $route->getResponseRedirect($input['method']);

        if ($redirectUrl) {
            $actionContext->getResponse()->setRedirectUrl($redirectUrl);
            return;
        }

        $actionContext->addAction(
            $route->getLayoutActionClassName($input['method']),
            ['actions' => $route->getActionClassNames($input['method'])],
            'layout'
        );

        $actionContext->getResponse()->setContentType($route->getResponseContentType($input['method']));
        $actionContext->getResponse()->setStatusCode($route->getResponseStatusCode($input['method']));
    }
}