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
    private $widgetClass = null;
    private $parentWidgetClass = null;
    private $parentWidgetId = null;
    private $row = [];
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

        $this->widgetClass = get_class($widget);
        $this->parentWidgetClass = $widget->getParentWidgetClass();
        $this->parentWidgetId = $widget->getParentWidgetId();
    }

    /**
     * @return null
     */
    public function getParentWidgetClass()
    {
        return $this->parentWidgetClass;
    }

    /**
     * @return null|string
     */
    public function getWidgetClass()
    {
        return $this->widgetClass;
    }

    /**
     * @return null
     */
    public function getParentWidgetId()
    {
        return $this->parentWidgetId;
    }

    public function build(array $row)
    {
        $this->row = $row;

        return parent::build($row);
    }

    /**
     * @return null
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @return null
     * @throws Error
     */
    public function getRoute()
    {
//        if ($this->route !== null) { // todo: Где вызывается перый раз, надо искать
//            return $this->route;
//        }

        $route = $this->getOption('route');

        if (!$route) {
            return $this->route;
        }

        if (is_string($route)) {
            $route = ['name' => $route];
        }

        if ($route === true) {
            $route = ['name' => true];
        }

        $this->route = array_merge(
            [
                'name' => true,
                'params' => [],
                'withGet' => false,
                'withDomain' => false,
                'method' => 'POST'
            ],
            (array)$route
        );

        if ($this->route['name'] === true) {
            $this->route['name'] = $this->getComponentName();
        }

        if (isset($this->route[0])) {
            throw new Error('Use deprecated init route. Define named options', $this);
        }

        $routeParams = [];

        $row = $this->getRow();

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



        return $this->route;
    }

    /**
     * @param null $route
     * @return null
     */
    public function setRoute($route)
    {
        return $this->route = $route;
    }

    /**
     * @return null
     * @throws Error
     */
    public function getHref()
    {
        if ($this->href !== null) {
            return $this->href;
        }

        $this->setHref($this->getOption('href', null));

        $route = $this->getRoute();

        if ($route && !$this->href) {
            try {
                return $this->setHref(Router::getInstance()->getUrl([$route['name'], $route['params'], $route['withGet'], $route['withDomain']]));
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

        return $this->href ? $this->href : Request::uri();
    }

    /**
     * @param null $href
     * @return null
     */
    public function setHref($href)
    {
        return $this->href = $href;
    }

    public function isActive()
    {
        if ($this->getOption('active') === null) {
            if ($href = $this->getHref()) {
                return $this->setActive(String::startsWith(Request::uri(), $href));
            }
        }

        return parent::isActive();
    }

    /**
     * @return null
     */
    public function getEvent()
    {
        $event = array_intersect_key($this->getOption(), array_flip(['onclick', 'onchange', 'submit']));

        if (!$event) {
            return null;
        }

        $eventType = key($event);
        $event = reset($event);

        if ($event === null) {
            return null;
        }

        if (is_string($event)) {
            $event = ['action' => $event];
        }

        if ($event === true) {
            $this->event = [
                'type' => 'onclick',
                'action' => Render::class,
                'params' => ['widgets' => [$this->getParentWidgetId() => $this->getParentWidgetClass()]],
                'ajax' => true,
                'callback' => null,
                'confirm_massage' => null,
                'code' => ''
            ];
        } else {
            $this->event = [
                'type' => $eventType,
                'class' => empty($event['action']) ? Render::class : $event['action'],
                'params' => empty($event['params']) ? [] : (array)$event['params'],
                'ajax' => isset($event['ajax']) ? $event['ajax'] : true,
                'callback' => empty($event['callback']) ? null : $event['callback'],
                'confirm_message' => empty($event['confirm_message']) ? null : $event['confirm_message'],
                'code' => empty($event['code']) ? '' : $event['code'],
            ];

            if ($this->event['class'][0] == '_') {
                $this->event['class'] = $this->getWidgetClass() . $this->event['class'];
            }

            $this->event['class'] = Action::getClass($this->event['class']);
        }

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

        if ($event['code']) {
            return $event['code'];
        }

        $code = 'Ice_Core_Widget.click($(this), \'' . $this->getHref() . '\', \'' . $this->getMethod() . '\'';

        if (isset($event['callback'])) {
            $code .= ', ' . $event['callback'];
        }

        if (isset($event['confirm_message'])) {
            $code .= isset($event['callback'])
            ? ', \'' . $event['confirm_message'] . '\''
            : ', null, \'' . $event['confirm_message'] . '\'';
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
        $route = $this->getRoute();

        return isset($route['method']) ? $route['method'] : 'POST';
    }

    public function getHtmlTagAttributes()
    {
        $htmlTagAttributes = '';

        if ($style = $this->getOption('stile', null)) {
            if ($htmlTagAttributes) {
                $htmlTagAttributes .= ' ';
            }

            $htmlTagAttributes .= 'style"=' . $style . '"';
        }

        if ($style = $this->getOption('style', null)) {
            if ($htmlTagAttributes) {
                $htmlTagAttributes .= ' ';
            }

            $htmlTagAttributes .= 'style"=' . $style . '"';
        }

        if ($title = $this->getOption('title', null)) {
            if ($htmlTagAttributes) {
                $htmlTagAttributes .= ' ';
            }

            $htmlTagAttributes .= 'title"=' . $title . '"';
        }

        return $htmlTagAttributes;
    }

    protected function buildParams($values)
    {
        if ($route = $this->getRoute()) {
            if ($this->getOption('value') === null) {
                $valueKey = $this->getValueKey();

                if ($this->get($valueKey) === null) {
                    $this->set($valueKey, Resource::create(Route::getClass())->get($route['name'], $route['params']));
                }
            }
        }

        parent::buildParams($values);
    }
}
