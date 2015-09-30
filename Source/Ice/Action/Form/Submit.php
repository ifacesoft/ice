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
use Ice\Core\Logger;
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
            'view' => ['template' => null, 'viewRenderClass' => 'Ice:Php', 'layout' => null],
            'input' => [
                'widget' => [
                    'providers' => 'request',
                    'validators' => 'Ice:Not_Empty'
                ],
                'resourceClass' => null
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
        $formClass = Widget::getClass($input['widget']['class']);

        $form = $formClass::getInstance($input['widget']['name']);

        $logger = $input['resourceClass']
            ? Logger::getInstance($input['resourceClass'])
            : $form::getLogger();

        try {
            return array_merge(
                ['success' => $logger->info('Submitted successfully', Logger::SUCCESS)],
                $form->submit($input['widget']['token'])
            );
        } catch (\Exception $e) {
            $message = ['Submit failed: {$0}', $e->getMessage()];

            $logger->error($message, __FILE__, __LINE__, $e);

            return ['error' => $logger->info($message, Logger::DANGER)];
        }
    }
}
