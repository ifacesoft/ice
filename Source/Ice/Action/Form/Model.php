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
use Ice\Helper\Arrays;

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
 * @version 0.0
 * @since 0.0
 */
class Form_Model extends Action
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
        'defaultViewRenderClassName' => 'Ice:Smarty',
        'inputValidators' => [
            'submitTitle' => 'Ice:Not_Empty',
            'modelClassName' => 'Ice:Not_Empty',
            'pk' => 'Ice:Not_Null',
            'formFilterFields' => 'Ice:Not_Empty'
        ],
        'inputDefaults' => [
            'grouping' => 1,
            'submitActionName' => 'Ice:Form_Submit',
            'reRenderClosest' => 'Ice:Form_Model',
            'reRenderActionNames' => [],
            'redirect' => '',
            'params' => []
        ],
//        'layout' => Emmet::PANEL_BODY
    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        /** @var Model $modelClass */
        $modelClass = Model::getClass($input['modelClassName']);

        $form = $modelClass::getForm($input['formFilterFields']);

        if ($input['pk']) {
            $form->bind($modelClass::getRow($input['pk'], '*'));
        }

        $actionContext->addAction(
            'Ice:Form',
            [
                'form' => $form,
                'submitActionName' => $input['submitActionName'],
                'reRenderClosest' => $input['reRenderClosest'],
                'reRenderActionNames' => $input['reRenderActionNames'],
                'grouping' => $input['grouping'],
                'submitTitle' => $input['submitTitle'],
                'redirect' => $input['redirect'],
                'params' => $input['params']
            ]
        );

        $input['formFilterFields'] = Arrays::toJsArrayString($input['formFilterFields']);
        $input['params'] = Arrays::toJsObjectString($input['params']);
        $input['reRenderActionNames'] = Arrays::toJsArrayString($input['reRenderActionNames']);

        return $input;
    }
}