<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Security_Provider;

/**
 * Class Security_Login
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context;
 * @package Ice\Action;
 * @author dp <email>
 * @version 0
 * @since 0
 */
class Security_Login extends Action
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
        'viewRenderClassName' => 'Ice:Php',
        'inputDefaults' => [
            'securityProviderName' => 'Login_Password'
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
        $securityProvider = Security_Provider::getInstance($input['securityProviderName']);
        $actionContext->addAction('Ice:Form', ['form' => $securityProvider->getLoginForm()]);
    }
}