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
use Ice\Core\Widget;
use Ice\DataProvider\Request;
use Ice\Helper\Access;

abstract class Widget_Event extends Render
{
    protected function initInput(array $configInput, array $data = [])
    {
        parent::initInput($configInput, $data);

        $input = $this->getInput();

        if (is_array($input['widget'])) {
            /** @var Widget $widgetClass */
            $widgetClass = Widget::getClass($input['widget']['class']);

            $widget = $widgetClass::getInstance($input['widget']['name']);

            $widget->setResource($input['widget']['resourceClass']);

            $widget->checkToken($input['widget']['token']);

            Access::check($widget->getActionAccess(get_class($this)));

            $request = Request::getInstance();

            foreach ($widget->getParts() as $part) {
                $widget->bind([$part['name'] => $request->get($part['name'])]);
            }

            $input['widget'] = $widget;
        }

        $this->setInput($input);
    }

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
}
