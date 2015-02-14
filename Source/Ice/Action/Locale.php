<?php namespace Ice\Action;


use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Request;
use Ice\Data\Provider\Router;
use Ice\Exception\Redirect;
use Ice\Helper\Api_Yandex;

class Locale extends Action
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
        'template' => '',
        'inputDataProviderKeys' => Router::DEFAULT_DATA_PROVIDER_KEY,
        'inputDefaults' => ['locale' => '']
    ];

    /** Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     * @throws Redirect
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        if (in_array($input['locale'], Api_Yandex::getLocales())) {
            $_SESSION['locale'] = $input['locale'];
        }

        if (Request::referer()) {
            throw new Redirect(Request::referer());
        }

        throw new Redirect('/');


    }
}