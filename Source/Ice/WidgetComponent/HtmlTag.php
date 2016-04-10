<?php

namespace Ice\WidgetComponent;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Request;
use Ice\Core\Resource;
use Ice\Core\Route;
use Ice\Core\Router;
use Ice\Core\Widget;
use Ice\Core\WidgetComponent;
use Ice\Core\Widget as Core_Widget;
use Ice\Exception\Error;
use Ice\Exception\RouteNotFound;
use Ice\Helper\Json;
use Ice\Helper\String;

class HtmlTag extends WidgetComponent
{
    private $active = null;
    private $route = null;
    private $href = null;

    /**
     * WidgetComponent config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'WidgetComponent: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    public function __construct($name, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($name, $options, $template, $widget);

        if (!empty($options['active'])) {
            $this->active = (bool)$options['active'];
        }

        $this->partEvents($options);
//        $this->initRoute($options);
    }

    public function build(array $row, Widget $widget)
    {
        /** @var HtmlTag $component */
        $component = parent::build($row, $widget);

        return $component
            ->buildRoute($row)
            ->buildRouteLabel($row)
            ->buildHref($row)
            ->buildActive($row);
    }
    
    protected function buildRoute($row)
    {
        $route = $this->getOption('route');

        if (!$route) {
            return $this;
        }

        if ($route === true) {
            $route = $this->getComponentName();
        }

        $route = (array)$route;

        $tempRouteParams = [];
        $withGet = false;
        $withDomain = false;

        if (count($route) == 4) {
            list($routeName, $tempRouteParams, $withGet, $withDomain) = $route;
        } elseif (count($route) == 3) {
            list($routeName, $tempRouteParams, $withGet) = $route;
        } elseif (count($route) == 2) {
            list($routeName, $tempRouteParams) = $route;
        } else {
            $routeName = reset($route);
        }

        $routeParams = [];

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

        $this->setRoute([$routeName, $tempRouteParams, $withGet, $withDomain]);

        return $this;

    }

    /**
     * @return null
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param null $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    protected function buildRouteLabel($params)
    {
        $route = $this->getRoute();

        if ($route && !$this->getOption('label')) {
            list($routeName, $routeParams, $withGet, $withDomain) = $route;
            $this->setLabel(Resource::create(Route::getClass())->get($routeName, $routeParams));

            if ($resource = $this->getResource()) {
                $this->setLabel($resource->get($this->getLabel()), $params);
            }
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param null $href
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

    protected function buildHref($params)
    {
        $this->setHref($this->getOption('href'));

        $route = $this->getRoute();

        if ($route && !$this->getHref()) {
            list($routeName, $routeParams, $withGet, $withDomain) = $route;
            try {
                $this->setHref(Router::getInstance()->getUrl([$routeName, $routeParams, $withGet, $withDomain]));
            } catch (\Exception $e) {
                throw new Error(
                    [
                        'Url generation was failed for route {$0} in widget {$1}  (part: {$2})',
                        [$routeName, $this->getWidgetId(), $this->getComponentName()]
                    ],
                    [$routeName, $routeParams, $withGet, $withDomain]
                );
            }
        }

        return $this;
    }


    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool|null $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    protected function buildActive($params)
    {
        if ($href = $this->getHref() && $this->getOption('active') === null) {
            $this->setActive(String::startsWith(Request::uri(), $href));
        } else {
            $this->setActive((bool) $this->getOption('active'));
        }

        return $this;
    }

    private function partEvents(array &$options)
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
}