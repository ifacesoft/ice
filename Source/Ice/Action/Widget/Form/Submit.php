<?php
/**
 * Ice action form submit class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Logger;
use Ice\Core\Request;
use Ice\Core\Widget_Form;
use Ice\Helper\Object;

/**
 * class Widget_Form_Submit
 *
 * Action submit model form
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 */
class Widget_Form_Submit extends Action
{
    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected static function config()
    {
        return [
            'view' => ['template' => ''],
            'input' => [
                'formClass' => [
                    'providers' => 'request',
                    'validators' => 'Ice:Not_Empty'
                ],
                'token' => [
                    'providers' => 'request',
                    'validators' => 'Ice:Not_Empty'
                ],
                'name' => [
                    'providers' => 'request',
                    'validators' => 'Ice:Not_Empty'
                ],
                'redirect' => [
                    'providers' => 'request',
                ]
            ],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   0.0
     */
    public function run(array $input)
    {
        /**
         * @var Widget_Form $formClass
         */
        $formClass = Widget_Form::getClass($input['formClass']);
        unset($input['formClass']);

        $redirect = $input['redirect'];
        unset($input['redirect']);

        try {
            $formClass::create(Request::uri(), __CLASS__)
                ->bind($input)
                ->submit();

            return [
                'success' => Widget_Form_Submit::getLogger()->info('Submitted successfully', Logger::SUCCESS),
                'redirect' => $redirect
            ];
        } catch (\Exception $e) {
            $message = ['Submit failed: {$0}', $e->getMessage()];

            Widget_Form_Submit::getLogger()->error($message, __FILE__, __LINE__, $e);

            return [
                'error' => Widget_Form_Submit::getLogger()->info($message, Logger::DANGER)
            ];
        }
    }
}
