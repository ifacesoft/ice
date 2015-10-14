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
    protected function initInput(array $configInput, array $data = [])
    {
        parent::initInput($configInput, $data);

        $input = $this->getInput();

        /** @var Widget $widgetClass */
        $widgetClass = Widget::getClass($input['widget']['class']);

        $widget = $widgetClass::getInstance($input['widget']['name']);

        $widget->setResource($input['widget']['resourceClass']);

        $widget->checkToken($input['widget']['token']);
        $widget->checkAction(get_class($this));

        $input['widget'] = $widget;

        $this->setInput($input);
    }
}
