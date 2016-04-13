<?php

namespace Ice\WidgetComponent;

use Ice\Action\Render;
use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Request;
use Ice\Core\Resource;
use Ice\Core\Route;
use Ice\Core\Router;
use Ice\Core\WidgetComponent;
use Ice\Core\Widget as Core_Widget;
use Ice\Exception\Error;
use Ice\Helper\Json;
use Ice\Helper\String;

class HtmlTag extends WidgetComponent
{
    private $route = null;
    private $href = null;
    private $event = null;

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

    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($componentName, $options, $template, $widget);
    }

    public function build(array $row, Core_Widget $widget)
    {
        /** @var HtmlTag $component */
        $component = parent::build($row, $widget);

        return $component
            ->buildRoute($row)
            ->buildRouteLabel($this->getParams())
            ->buildHref()
            ->buildEvent($widget);
    }

    protected function buildRoute($row)
    {
        $route = $this->getOption('route');

        if (!$route) {
            return $this;
        }

        if (is_string($route)) {
            $route = ['name' => $route];
        }

        if ($route === true) {
            $route = ['name' => $this->getComponentName()];
        }

        $this->route = array_merge(
            [
                'name' => $this->getComponentName(),
                'params' => [],
                'withGet' => false,
                'withDomain' => false,
                'method' => 'POST'

            ],
            (array)$route
        );

        if (isset($this->route[0])) {
            throw new Error('Use deprecated init route. Define named options', $this);
        }

        $routeParams = [];

        foreach ((array)$this->route['params'] as $routeParamKey => $routeParamValue) {
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

        $this->route['params'] = $routeParams;

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
            $this->setLabel(Resource::create(Route::getClass())->get($route['name'], $route['params']));

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

    protected function buildHref()
    {
        $this->setHref($this->getOption('href'));

        $route = $this->getRoute();

        if ($route && !$this->getHref()) {
            try {
                $this->setHref(Router::getInstance()->getUrl([$route['name'], $route['params'], $route['withGet'], $route['withDomain']]));
            } catch (\Exception $e) {
                throw new Error(
                    [
                        'Url generation was failed for route {$0} in widget {$1}  (part: {$2})',
                        [$route['name'], $this->getWidgetId(), $this->getComponentName()]
                    ],
                    $this
                );
            }
        }

        return $this;
    }

    protected function buildActive()
    {
        parent::buildActive();

        if ($this->getOption('active') !== null) {
            if ($href = $this->getHref()) {
                $this->setActive(String::startsWith(Request::uri(), $href));
            }
        }

        return $this;
    }

    private function buildEvent(Core_Widget $widget)
    {
        $event = array_intersect_key($this->getOption(), array_flip(['onclick', 'onchange', 'submit']));

        if (!$event) {
            return $this;
        }

        $eventType = key($event);
        $event = reset($event);

        if ($event === true) {
            $this->event = $widget->getRenderEvent();
        } else {
            $this->event = [
                'type' => $eventType,
                'class' => empty($event['action']) ? Render::class : $event['action'],
                'params' => empty($event['params']) ? [] : (array)$event['params'],
                'callback' => empty($event['callback']) ? null : $event['callback'],
                'confirm_message' => empty($event['confirm_message']) ? null : $event['confirm_message']
            ];

            if ($this->event['class'][0] == '_') {
                $this->event['class'] = get_class($widget) . $this->event['class'];
            }

            $this->event['class'] = Action::getClass($this->event['class']);
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param null $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getDataParams()
    {
        return Json::encode($this->getParams());
    }

    public function getDataAction()
    {
        return Json::encode($this->getEvent());
    }

    public function getEventCode()
    {
        $event = $this->getEvent();

        if (!$event) {
            return '';
        }

        $code = 'Ice_Core_Widget.click($(this), \'' . $this->getHref() . '\', \'' . $this->getMethod() . '\'';

        if (isset($event['callback'])) {
            $code .= ', ' . $event['callback'];
        }

        if (isset($event['confirm_message'])) {
            $code .= ', \'' . $event['confirm_message'] . '\'';
        }

        return $code . '); return false;';
    }

    public function getEventAttributesCode()
    {
        $event = $this->getEvent();

        if (!$event) {
            return '';
        }

        $code = ' ';

        switch ($event['type']) {
            case 'submit':
                $code .= 'onsubmit="';
                break;
            case 'onchange':
                $code .= 'onchange="';
                break;
            default:
                $code .= 'onclick="';
        }

        return $code . $this->getEventCode() . '" data-action=\'' . $this->getDataAction() . '\' data-params=\'' . $this->getDataParams() . '\'';
    }

    public function getMethod()
    {
        return $this->getRoute()['method'];
    }
}
