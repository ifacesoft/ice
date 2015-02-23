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
    /**
     * Action config
     *
     * example:
     * ```php
     *  $config = [
     *      'actions' => [
     *          ['Ice:Title', ['title' => 'page title'], 'title'],
     *          ['Ice:Another_Action, ['param' => 'value']
     *      ],
     *      'view' => [
     *          'layout' => Emmet::PANEL_BODY,
     *          'template' => _Custom,
     *          'viewRenderClass' => Ice:Twig,
     *      ],
     *      'input' => [
     *          Request::DEFAULT_DATA_PROVIDER_KEY => [
     *              'paramFromGETorPOST => [
     *                  'default' => 'defaultValue',
     *                  'validators' => ['Ice:PATTERN => PATTERN::LETTERS_ONLY]
     *                  'type' => 'string'
     *              ]
     *          ]
     *      ],
     *      'output' => ['Ice:Resource/Ice\Action\Index'],
     *      'ttl' => 3600,
     *      'roles' => []
     *  ];
     * ```
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Smarty'],
            'input' => [
                'submitTitle' => ['validators' => 'Ice:Not_Empty'],
                'modelClassName' => ['validators' => 'Ice:Not_Empty'],
                'pk' => ['validators' => 'Ice:Not_Null'],
                'formFilterFields' => ['validators' => 'Ice:Not_Empty'],
                'grouping' => ['default' => 1],
                'submitActionName' => ['default' => 'Ice:Form_Submit'],
                'reRenderClosest' => ['default' => 'Ice:Form_Model'],
                'reRenderActionNames' => ['default' => []],
                'redirect' => ['default' => ''],
                'params' => ['default' => []]
            ]
        ];
    }

    /**
     * Run action
     *
     * @param array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function run(array $input)
    {
        /** @var Model $modelClass */
        $modelClass = Model::getClass($input['modelClassName']);

        $form = $modelClass::getForm($input['formFilterFields']);

        if ($input['pk']) {
            $form->bind($modelClass::getRow($input['pk'], '*'));
        }

        $this->addAction([
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
        ]);

        $input['formFilterFields'] = Arrays::toJsArrayString($input['formFilterFields']);
        $input['params'] = Arrays::toJsObjectString($input['params']);
        $input['reRenderActionNames'] = Arrays::toJsArrayString($input['reRenderActionNames']);

        return $input;
    }
}