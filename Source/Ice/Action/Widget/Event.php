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
use Ice\Core\Widget;
use Ice\Widget\Form;

abstract class Widget_Event extends Render
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
                'action' => [
                    'providers' => 'request',
                    'validators' => 'Ice:Not_Empty'
                ]
            ],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }


    protected function initInput(array $configInput, array $data = [])
    {
        parent::initInput($configInput, $data);

        $input = $this->getInput();

        /** @var Widget $widgetClass */
        $widgetClass = Widget::getClass($input['widget']['class']);

        $widget = $widgetClass::getInstance($input['widget']['name']);

        $widget->setResource($input['widget']['resourceClass']);

        $widget->checkToken($input['widget']['token']);

        $input['widget'] = $widget;

        $this->setInput($input);
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
        $resource = $input['widget']->getResource();

        $logger = $resource ? Logger::getInstance(get_class($resource->getResourceClass())) : $input['widget']->getLogger();

        try {
            /** @var Action $actionClass */
            $actionClass = Action::getClass($input['action']['class']);

            return array_merge(
                [
                    'redirect' => $input['widget']->getRedirect(),
                    'timeout' => $input['widget']->getTimeout()
                ],
                $actionClass::call($input['action']['params'])
            );
        } catch (\Exception $e) {
            $message = ['Event failed: {$0}', $e->getMessage()];

            $logger->error($message, __FILE__, __LINE__, $e);

            return ['error' => $logger->info($message, Logger::DANGER)];
        }
    }
}
