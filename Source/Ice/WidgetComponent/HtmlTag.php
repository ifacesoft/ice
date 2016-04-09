<?php

namespace Ice\WidgetComponent;

use Ice\Core\Action;
use Ice\Core\Router;
use Ice\Core\WidgetComponent;
use Ice\Core\Widget as Core_Widget;
use Ice\Exception\Error;
use Ice\Exception\RouteNotFound;
use Ice\Helper\Json;

class HtmlTag extends WidgetComponent
{
    private $active = false;

    public function __construct($name, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($name, $options, $template, $widget);

        if (!empty($options['active'])) {
            $this->active = (bool)$options['active'];
        }

        $this->partEvents($name, $options);
        $this->partRoute($name, $options);
    }

    private function partRoute($name, $options)
    {
        if (!empty($part['options']['route'])) {
            if ($part['options']['route'] === true) {
                $part['options']['route'] = $partName;
            }

            $part['options']['route'] = (array)$part['options']['route'];
            $tempRouteParams = [];
            $withGet = false;
            $withDomain = false;

            if (count($part['options']['route']) == 4) {
                list($routeName, $tempRouteParams, $withGet, $withDomain) = $part['options']['route'];
            } elseif (count($part['options']['route']) == 3) {
                list($routeName, $tempRouteParams, $withGet) = $part['options']['route'];
            } elseif (count($part['options']['route']) == 2) {
                list($routeName, $tempRouteParams) = $part['options']['route'];
            } else {
                $routeName = reset($part['options']['route']);
            }

            $routeParams = [];//$part['params'];

            foreach ((array)$tempRouteParams as $routeParamKey => $routeParamValue) {
                if (is_int($routeParamKey)) {
                    $routeParams[$routeParamValue] = !is_array($routeParamValue) && array_key_exists($routeParamValue, $row)
                        ? $row[$routeParamValue]
                        : $routeParamValue;

                    continue;
                }

                $routeParams[$routeParamKey] = !is_array($routeParamValue) && array_key_exists($routeParamValue, $row)
                    ? $row[$routeParamValue]
                    : $routeParamValue;
            }

            if (isset($part['options']['render'])) {
                if ($part['options']['render'] === true) {
                    $part['options']['render'] = isset($part['params'][$part['value']]);
                } else {
                    $part['options']['render'] = isset($part['params'][$part['options']['render']]);
                }
            } else {
                $part['options']['render'] = true;
            }

            if (!$part['options']['render']) {
                continue;
            }

            if (!array_key_exists('href', $part['options'])) {
                try {
                    $part['options']['href'] = Router::getInstance()->getUrl([$routeName, $routeParams, $withGet, $withDomain]);
                } catch (\Exception $e) {
                    throw new Error(
                        [
                            'Url generation was failed for route {$0} in widget {$1}  (part: {$2})',
                            [$routeName, $widgetClass::getClassName(), $partName]
                        ],
                        [$routeName, $routeParams, $withGet, $withDomain]
                    );
                }
            }

            if (!array_key_exists('active', $part['options'])) {
                $part['options']['active'] = String::startsWith(Request::uri(), $part['options']['href']);
            }

            if (!array_key_exists('label', $part['options']) && $routeName == $partName) {
                $part['label'] = Resource::create(Route::getClass())->get($routeName, $routeParams);

                $part['label'] = $part['label'] && $part['resource']
                    ? $part['resource']->get($part['label'], $resourceParams)
                    : $part['label'];
            }
        }
    }

    private function partEvents($partName, array &$options)
    {
        foreach (['onclick', 'onchange', 'submit'] as $event) {

            if (array_key_exists($event, $options)) {
                if ($options[$event] === true) {
                    throw new Error(
                        ['Temporary For part {$0} with event {$1} of widget {$2} must be defined action param',
                            [$partName, $event, get_class($this)]
                        ]
                    );
                }

                if (is_string($options[$event])) {
                    $options['dataAction'] = Json::encode([
                        'class' => '',
                        'data' => []
                    ]);
                    continue;
                }

                $actionData = [];

                if (isset($options[$event]['action'])) {
                    if ($options[$event]['action'][0] == '_') {
                        /** @var Widget $widgetClass */
                        $widgetClass = get_class($this);
                        $options[$event]['action'] = $widgetClass::getModuleAlias() . ':' .
                            $widgetClass::getClassName() . $options[$event]['action'];
                    }

                    $actionData['class'] = Action::getClass($options[$event]['action']);
                    unset($options[$event]['action']);
                }

                if (isset($options[$event]['data'])) {
                    $actionData['data'] = $options[$event]['data'];
                    unset($options[$event]['data']);
                }

                if (isset($options[$event]['url'])) {
                    try {
                        $options[$event]['url'] = $options[$event]['url'] === true
                            ? Router::getInstance()->getUrl($partName)
                            : Router::getInstance()->getUrl($options[$event]['url']);
                    } catch (RouteNotFound $e) {
                        $options[$event]['url'] = '/';
                    }
                }

                $options['url'] = isset($options[$event]['url']) ? $options[$event]['url'] : '/';
                $options['method'] = isset($options[$event]['method']) ? $options[$event]['method'] : 'POST';

                $actionData['ajax'] = array_key_exists('ajax', $options[$event]) ? $options[$event]['ajax'] : true;

                $options['dataAction'] = Json::encode($actionData);

                $options[$event] = $actionData['ajax']
                    ? $this->getOnclick($options['url'], $options['method'], $options[$event])
                    : '';
            }
        }
    }

    /**
     * @param $url
     * @param $method
     * @param array $event
     * @return string
     */
    protected function getOnclick($url, $method, array $event)
    {
        $code = 'Ice_Core_Widget.click($(this), \'' . $url . '\', \'' . $method . '\'';

        if (isset($event['callback'])) {
            $code .= ', ' . $event['callback'];
        }

        return $code . '); return false;';
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }
}