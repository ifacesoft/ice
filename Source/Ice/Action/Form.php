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
use Ice\Helper\Emmet;
use Ice\Helper\Object;
use Ice\View\Render\Php;
use Ice\View\Render\Replace;

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

        $formName = 'Form_' . Object::getName($formClass);

        $filterFields = $form->getFilterFields();

        $fields = $form->getFields();
        $values = $form->getKey();

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

        foreach ($targetFields as $fieldName => $field) {
            $result[] = Php::getInstance()->fetch(
                Ui_Form::getClass($formClass . '_' . $field['template']),
                [
                    'fieldName' => $fieldName,
                    'value' => $values[$fieldName],
                    'options' => $field['options'],
                    'formName' => $formName,
                    'title' => $field['title']
                ]
            );
        }

        return [
            'fields' => $result,
            'classes' => $form->getClasses()
        ];
    }
}