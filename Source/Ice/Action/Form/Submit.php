<?php
/**
 * Ice action form submit class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Form as Core_Form;
use Ice\Core\Logger;

/**
 * Class Form_Submit
 *
 * Action submit model form
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
class Form_Submit extends Action
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
            'view' => ['template' => ''],
            'input' => [
                'default' => [
                    'formClass' => ['validators' => 'Ice:Not_Empty'],
                    'formKey' => ['validators' => 'Ice:Not_Empty'],
                    'filterFields' => ['default' => ''],
                    'redirect' => ['default' => '']
                ]
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
     * @version 0.0
     * @since 0.0
     */
    public function run(array $input)
    {
        /** @var Core_Form $formClass */
        $formClass = $input['formClass'];
        unset($input['formClass']);

        $formKey = $input['formKey'];
        unset($input['formKey']);

        $filterFields = empty($input['filterFields']) ? [] : explode(',', $input['filterFields']);
        unset($input['filterFields']);

        $redirect = $input['redirect'];
        unset($input['redirect']);

        try {
            $formClass::getInstance($formKey)
                ->addFilterFields($filterFields)
                ->bind($input)
                ->submit();

            return [
                'success' => Form_Submit::getLogger()->info('Submitted successfully', Logger::SUCCESS),
                'redirect' => $redirect
            ];
        } catch (\Exception $e) {
            $message = ['Submit failed: {$0}', $e->getMessage()];

            Form_Submit::getLogger()->error($message, __FILE__, __LINE__, $e);

            return [
                'error' => Form_Submit::getLogger()->info($message, Logger::DANGER)
            ];
        }
    }
}