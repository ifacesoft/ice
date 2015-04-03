<?php
/**
 * Ice action form class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Ui_Form;
use Ice\Helper\Arrays;
use Ice\Helper\Object;
use Ice\View\Render\Php;

/**
 * Class Form
 *
 * Default ice form action
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
class Form extends Action
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
            'view' => ['viewRenderClass' => 'Ice:Php', 'layout' => 'div.Form>div.panel.panel-default>div.panel-body'],
            'input' => [
                'form' => ['validators' => 'Ice:Is_Ui_Form'],
//                'submitTitle' => ['validators' => 'Ice:Not_Empty'],
                'grouping' => ['default' => 1],
                'submitActionName' => ['default' => 'Ice:Form_Submit'],
                'redirect' => ['default' => ''],
                'params' => ['default' => []],
                'reRenderClosest' => ['default' => ''],
                'reRenderActionNames' => ['default' => []]
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
     * @version 0.1
     * @since 0.0
     */
    public function run(array $input)
    {
        /** @var Ui_Form $form */
        $form = $input['form'];
        unset($input['form']);

        $formClass = get_class($form);

        $formKey = $form->getKey();

        $formName = 'Form_' . Object::getName($formClass) . '_' . Object::getName($formKey);

        $filterFields = $form->getFilterFields();
        $fields = $form->getFields();
        $values = $form->getValues();

        $result = [];

        $targetFields = [];
        foreach ($filterFields as $key => &$value) {
            if (is_string($key)) {
                if (is_array($value)) {
                    list($fields[$key]['type'], $fields[$key]['template']) = $value;
                } else {
                    $fields[$key]['type'] = $value;
                    $fields[$key]['template'] = 'Ice:Field_' . $value;
                }

                $value = $key;
            }

            $targetFields[$value] = $fields[$value];
            unset($fields[$value]);
        }

        if (empty($targetFields)) {
            $targetFields = $fields;
        }

        unset($fields);

        foreach ($targetFields as $fieldName => $scheme) {
            $data = [
                'formName' => $formName,
                'fieldName' => $fieldName,
                'value' => $values[$fieldName]
            ];

            $field = Php::getInstance()->fetch(Ui_Form::getClass($scheme['template']), array_merge($scheme, $data));

            if ($input['grouping']) {
                $result[$scheme['type']][] = $field;
            } else {
                $result[] = $field;
            }
        }

        return [
            'grouping' => $input['grouping'],
            'fields' => $result,
            'formName' => $formName,
            'formClass' => $formClass,
            'formKey' => $formKey,
            'submitActionName' => $input['submitActionName'],
//            'submitTitle' => $input['submitTitle'],
            'redirect' => $input['redirect'],
            'filterFields' => implode(',', $filterFields),
            'params' => Arrays::toJsObjectString($input['params']),
            'reRenderActionNames' => Arrays::toJsArrayString($input['reRenderActionNames']),
            'reRenderClosest' => $input['reRenderClosest']
        ];
    }
}