<?php
/**
 * Ice action form model class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Model;

/**
 * Class Form_Model
 *
 * Action for model form
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
class Form_Model extends Action
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
        'viewRenderClassName' => 'Ice:Smarty',
        'inputValidators' => [
            'submitClassName' => 'Ice:Not_Empty',
            'modelName' => 'Ice:Not_Empty',
            'pk' => 'Ice:Numeric_Positive'
        ],
        'inputDefaults' => [
            'groupping' => true,
            'reRenderClassNames' => []
        ],
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

        $form = $class::getForm();

        $model = $class::getModel($input['pk'], '*');

        if ($model) {
            $form->bindModel($model);
        }

        $data = [
            'form' => $form,
            'submitClassName' => $input['submitClassName'],
            'reRenderClassNames' => $input['reRenderClassNames']
        ];

        $actionContext->addAction('Ice:Form', $data);
    }
}