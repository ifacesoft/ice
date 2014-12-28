<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Form_Security_Register;

/**
 * Class Security_Register
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context;
 * @package Ice\Action;
 * @author dp <email>
 * @version 0
 * @since 0
 */
class Security_Register extends Action
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
        'defaultViewRenderClassName' => 'Ice:Php',
        'inputDefaults' => [
            'security' => 'Login_Password',
            'redirect' => '/security/login'
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
        $resource = Security_Register::getResource();

        $actionContext->addAction(
            'Ice:Form', [
                'form' => Form_Security_Register::getInstance($input['security']),
                'submitTitle' => $resource->get('Register'),
                'redirect' => $input['redirect']
            ]
        );
        return ['resource' => $resource];
    }
}