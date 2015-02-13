<?php namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Data_Scheme;
use Ice\Core\Data_Source;

class Orm_Scheme_Update extends Action
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
        'inputDefaults' => [
            'force' => 0
        ]
    ];

    /** Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @package Ice
     * @subpackage Action
     *
     * @version 0.5
     * @since 0.5
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        $output = [];

        foreach (Data_Source::getConfig()->gets() as $dataSourceClass => $config) {
            foreach ($config as $key => $schemes) {
                foreach ((array)$schemes as $scheme) {
                    $output[$key . '.' . $scheme] =
                        Data_Scheme::create($dataSourceClass . '/' . $key . '.' . $scheme)->update($input['force']);
                }
            }
        }

        return $output;
    }
}