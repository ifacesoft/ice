<?php
/**
 * Ice action front ajax class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Logger;
use Ice\Core\View;
use Ice\Data\Provider\Request;
use Ice\Helper\Object;

/**
 * Class Front_Ajax
 *
 * Action front for ajax request
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
class Front_Ajax extends Action
{
    /**  public static $config = [
     *      'staticActions' => [],          // actions
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
        'viewRenderClassName' => 'Ice:Json',
        'inputDataProviderKeys' => [Request::DEFAULT_KEY],
        'inputValidators' => [
            'call' => 'Ice:Not_Empty'
        ]
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
        if (empty($input['params'])) {
            $input['params'] = [];
        }

        if (is_string($input['params'])) {
            parse_str($input['params'], $input['params']);
        }

        $actionContext->addAction($input['call'], $input['params'], 'result');

        return [
            'back' => $input['back']
        ];
    }

    /**
     * Flush action context.
     *
     * Modify view after flush
     *
     * @param View $view
     * @return View
     */
    public function flush(View $view)
    {
        $view = parent::flush($view);

        $params = $view->getParams();

        if ($params['result'] instanceof View) {
            $params['result'] = [
                'actionName' => Object::getName($params['result']->getActionClass()),
                'data' => isset($params['result']->getParams()['data']) ? $params['result']->getParams()['data'] : [],
                'error' => isset($params['result']->getParams()['error']) ? $params['result']->getParams()['error'] : [],
                'success' => isset($params['result']->getParams()['success']) ? $params['result']->getParams()['success'] : [],
                'html' => $params['result']->getContent()
            ];
        } else {
            $params['result'] = [
                'actionName' => '',
                'data' => [],
                'error' => $params['result'],
                'success' => '',
                'html' => ''
            ];
        }

        $view->setParams($params);

        Logger::clearLog();

        return $view;
    }
}