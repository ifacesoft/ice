<?php
/**
 * Ice action model delete class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Logger;
use Ice\Core\Model;

/**
 * Class Model_Delete
 *
 * Action for delete options
 *
 * @todo WARNING!!! ONLY TEST USAGE
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
class Model_Delete extends Action
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
        'viewRenderClassName' => 'Ice:Php',
        'template' => '',
        'inputValidators' => [
            'modelName' => 'Ice:Not_Empty',
            'pk' => 'Ice:Numeric_Positive'
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
        $class = Model::getClass($input['modelName']);

        $class::getQueryBuilder()->delete($input['pk'])->getQuery()->getData();

        return [
            'data' => [
                'rollActionName' => 'Tp:Roll'
            ],
            'success' => Model_Delete::getLogger()->info(['Row {$0} of {$1} deleted successfully', [$input['pk'], $class::getClassName()]], Logger::SUCCESS)
        ];
    }
}