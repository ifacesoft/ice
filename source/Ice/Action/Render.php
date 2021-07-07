<?php

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Exception;
use Ice\Core\Widget;
use Ice\DataProvider\Request;
use Ice\Exception\Access_Denied;
use Ice\Exception\Http_Forbidden;

class Render extends Action
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['template' => '', 'viewRenderClass' => 'Ice:Php', 'layout' => ''],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [
                'content' => ['default' => null],
                'widgets' => ['default' => [], 'providers' => ['default', Request::class]]
            ],
            'output' => []
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     * @throws Http_Forbidden
     * @throws Exception
     */
    public function run(array $input)
    {
        if (!empty($input['content']) ) {
            $content = (array) $input['content'];

            if (count($content) > 1) {
                list ($widgetClass, $params) = $content;
            } else {
                $widgetClass = reset($content);
                $params = [];
            }

            try {
                if (is_array($widgetClass)) {
                    list($widgetClass, $config) = array_pad($widgetClass, 2, []);
                }

                /** @var Widget $widgetClass */
                $widgetClass = Widget::getClass($widgetClass);

                
                $widget = $widgetClass::getInstance(null, null, $params);
                
                if (isset($config['render']['template'])) {
                    $widget->setTemplateClass($config['render']['template']);
                }

                return ['content' => $widget->render()];
//                return ['content' => \Minify_HTML::minify($widget->render())];
            } catch (Access_Denied $e) {
                throw new Http_Forbidden('В доступе отказано', [], $e);
            }
        }

        $widgets = [];

        foreach ($input['widgets'] as $key => $widgetClass) {
            if (is_array($widgetClass)) {
                list($widgetClass, $params) = $widgetClass;
            } else {
                $params = [];
            }

            /** @var Widget $widgetClass */
            $widgetClass = Widget::getClass($widgetClass);

            if ($key === 'content') {
                $widgets['content'] = $widgetClass::getInstance($key, null, $params)->render();
            } else {
                $content = $widgetClass::getInstance($key, null, $params);

                $output = $content->getOutput();

                $widgets[$content->getWidgetId()] = [
                    'content' => $content->render(),
                    'params' => isset($output['callbackParams']) ? $output['callbackParams'] : [],
                    'callback' => isset($output['callback']) ? $output['callback'] : null
                ];
            }
        }

        return ['widgets' => $widgets];
    }
}