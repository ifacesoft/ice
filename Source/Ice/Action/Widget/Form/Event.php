<?php
/**
 * Ice action form submit class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Widget;

abstract class Widget_Form_Event extends Render
{
    public function run(array $input)
    {
        return array_merge(
            parent::run($input),
            [
                'redirect' => $input['widget']->getRedirect(),
                'timeout' => $input['widget']->getTimeout()
            ]
        );
    }

    protected function initInput(array $configInput, array $data = [])
    {
        parent::initInput($configInput, $data);

        $input = $this->getInput();

        if (is_array($input['widget'])) {
            /** @var Widget $widgetClass */
            $widgetClass = Widget::getClass($input['widget']['class']);

            $widget = $widgetClass::getInstance($input['widget']['name']);

            $widget->setResourceClass($input['widget']['resourceClass']); // todo: @deprecated

            $widget->checkToken($input['widget']['token']);

            $input['widget'] = $widget;
        }

        $this->setInput($input);
    }
}
