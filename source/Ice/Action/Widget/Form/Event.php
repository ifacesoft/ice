<?php
/**
 * Ice action form submit class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Exception;
use Ice\Core\Widget;
use Ice\DataProvider\Request;
use Ice\Exception\Http_Forbidden;

abstract class Widget_Form_Event extends Render
{
    protected static function config()
    {
        $config = parent::config();

        $config['input'] = array_merge(
            $config['input'],
            [
                'widget' => ['default' => null, 'providers' => ['default', Request::class]], // @depticated
                'widgetClass' => ['default' => null, 'providers' => ['default', Request::class]],
                'widgetName' => ['default' => null, 'providers' => ['default', Request::class]],
                'widgetToken' => ['default' => null, 'providers' => ['default', Request::class]],
            ]
        );

        return $config;
    }

    /**
     * @param array $input
     *
     * @return array
     * @throws Exception
     * @throws Http_Forbidden
     * @todo в этом методе не должно быть реализации - вынеси в другой метод (чилдренские классы должны видеть что нужно его реализовать самим)
     */
    public function run(array $input)
    {
        $output = [];

        if (isset($input['widget'])) {
            $output['redirect'] = $input['widget']->getRedirect();
            $output['timeout'] = $input['widget']->getTimeout();

            $input['widget']->removeInstance();

            unset($input['widget']);
        }

        return array_merge(parent::run($input), $output);
    }

    protected function initInput(array $configInput, array $data = [])
    {
        parent::initInput($configInput, $data);

        $input = $this->getInput();

        // @depricated $input['widget'][], use $input['widgetClass'], $input['widgetName']

        if (!is_object($input['widget'])) {
            /** @var Widget $widgetClass */
            $widgetClass = null;

            if (isset($input['widgetClass'])) {
                $widgetClass = Widget::getClass($input['widgetClass']);
            }

            if (!$widgetClass && isset($input['widget']['class'])) {
                $widgetClass = Widget::getClass($input['widget']['class']);
            }

            $widgetName = null;

            if (isset($input['widgetName'])) {
                $widgetName = $input['widgetName'];
            }

            if (!$widgetName && isset($input['widget']['name'])) {
                $widgetName = $input['widget']['name'];
            }

            if ($widgetClass && $widgetName) {
                $widget = $widgetClass::getInstance($widgetName);

                $widgetToken = null;

                if (isset($input['widgetToken'])) {
                    $widgetToken = $input['widgetToken'];
                }

                if (!$widgetToken && isset($input['widget']['token'])) {
                    $widgetToken = $input['widget']['token'];
                }

                $widget->checkToken($widgetToken);

                $input['widget'] = $widget;
            }
        }

        $this->setInput($input);
    }
}
