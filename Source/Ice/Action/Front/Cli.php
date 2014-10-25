<?php
/**
 * Ice action front cli class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\View;
use Ice\Data\Provider\Cli as Data_Provider_Cli;

/**
 * Class Front_Cli
 *
 * Action front via console
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
class Front_Cli extends Action
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
        'viewRenderClassName' => 'Ice:Cli',
        'inputDataProviderKeys' => [Data_Provider_Cli::DEFAULT_KEY],
        'inputDefaults' => [
            'action' => [
                'default' => 'Ice:Module_Deploy',
                'title' => 'Action [{$0}]: ',
                'validators' => [
                    'Ice:Pattern' => [
                        'params' => '/^[:a-z]+$/i',
                        'message' => 'Action mast conteints only letters and sign ":"'
                    ]
                ]
            ],
        ]
    ];

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

        $data = $view->getParams();

        if ($data['cli'] instanceof View) {
            $data['cli'] = $data['cli']->getContent();
        }

        $view->setParams($data);

        return $view;
    }

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        ini_set('memory_limit', '1024M');

        $actionContext->addAction($input['action'], $input, 'cli');
    }
}