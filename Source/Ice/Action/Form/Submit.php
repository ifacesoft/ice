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
use Ice\Core\View;
use Ice\Core\Widget;
use Ice\Widget\Form;

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
class Form_Submit extends Action
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
                'widgetClass' => [
                    'providers' => 'request',
                    'validators' => 'Ice:Not_Empty'
                ],
                'viewClass' => [
                    'providers' => 'request',
                    'default' => null
                ],
                'token' => [
                    'providers' => 'request',
                    'validators' => 'Ice:Not_Empty'
                ],
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
        /** @var Form $formClass */
        $formClass = Widget::getClass($input['widgetClass']);

        $form = $formClass::create()
            ->setViewClass(Widget::getClass($input['viewClass']));

        /** @var View $viewClass */
        $viewClass = $form->getViewClass();

        try {
            $form->init($form->getValues());

            return array_merge(
                ['success' => $viewClass::getLogger()->info('Submitted successfully', Logger::SUCCESS)],
                (array)$form->action($input['token'])
            );
        } catch (\Exception $e) {
            $message = ['Submit failed: {$0}', $e->getMessage()];

            $viewClass::getLogger()->error($message, __FILE__, __LINE__, $e);

            return [
                'error' => $viewClass::getLogger()->info($message, Logger::DANGER)
            ];
        }
    }
}
