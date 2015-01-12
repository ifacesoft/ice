<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 28.12.14
 * Time: 20:32
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Data\Provider\Request;

class Test extends Action
{

    /**
     *  public static $config = [
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
        'defaultViewRenderClassName' => 'Ice:Php',
        'inputDataProviderKeys' => [Request::DEFAULT_KEY],
        'afterActions' => [
            '_NoView',
            '_Smarty' => ['inputTestSmarty' => 'inputTestSmarty'],
            '_Twig' => ['inputTestTwig' => 'inputTestTwig'],
            '_Php' => [
                ['inputTestPhp' => 'inputTestPhp1'],
                ['inputTestPhp' => 'inputTestPhp2']
            ]
        ],
        'inputDefaults' => [
            'test' => 'test'
        ]
    ];

    /** Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        return [
            'test' => $input['test'],
        ];
    }
}