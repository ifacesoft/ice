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
use Ice\Core\Action_Context;
use Ice\Core\Form as Core_Form;
use Ice\Core\Model;
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
        'inputValidators' => [
            'form' => 'Ice:Is_Form'
        ],
        'inputDefaults' => [
            'groupping' => true,
            'submitActionName' => 'Ice:Form_Submit',
            'submitTitle' => 'Submit',
            'reRenderActionNames' => [],
            'redirect' => ''
        ],
        'layout' => 'div.Form>div.panel.panel-default>div.panel-body'
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
     * @version 0.1
     * @since 0.0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        /** @var Core_Form $form */
        $form = $input['form'];

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

            $field = Php::getInstance()->fetch(Core_Form::getClass($scheme['template']), array_merge($scheme, $data));

            if ($input['groupping']) {
                $result[$scheme['type']][] = $field;
            } else {
                $result[] = $field;
            }
        }

        return [
            'groupping' => $input['groupping'],
            'fields' => $result,
            'formName' => $formName,
            'formClass' => $formClass,
            'formKey' => $formKey,
            'submitActionName' => $input['submitActionName'],
            'reRenderActionNames' => empty($input['reRenderActionNames']) ? '' : implode(',', $input['reRenderActionNames']),
            'filterFields' => implode(',', $filterFields),
            'submitTitle' => $input['submitTitle'],
            'redirect' => $input['redirect']
        ];
    }
}