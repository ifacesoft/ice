<?php

namespace Ice\WidgetComponent;

use Ice\Action\Render;
use Ice\Core\Action;
use Ice\Core\Request;
use Ice\Core\Resource;
use Ice\Core\Route;
use Ice\Core\Router;
use Ice\Core\Widget as Core_Widget;
use Ice\Core\WidgetComponent;
use Ice\Exception\Error;
use Ice\Helper\Json;
use Ice\Helper\String;

class HtmlTag extends WidgetComponent
{
    private $event = null;
    private $parentWidgetClass = null;
    private $parentWidgetId = null;
    private $row = [];

    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($componentName, $options, $template, $widget);

        $this->parentWidgetClass = $widget->getParentWidgetClass();
        $this->parentWidgetId = $widget->getParentWidgetId();
    }

    /**
     * @return null
     */
    public function getRow()
    {
        return $this->row;
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
     * @throws Error
     */
    public function getHref()
    {
        $href = $this->getOption('href', null);

        if ($href) {
            return $href;
        }

        $route = $this->getRoute();

        if ($route) {
            try {
                return Router::getInstance()->getUrl([$route['name'], $route['params'], $route['withGet'], $route['withDomain']]);
            } catch (\Exception $e) {
                throw new Error(
                    [
                        'Url generation was failed for route {$0} in widget {$1}  (part: {$2})',
                        [$route['name'], $this->getWidgetId(), $this->getComponentName()]
                    ],
                    $this,
                    $e
                );
            }
        }

        return Request::uri();
    }

    /**
     * @return null
     * @throws Error
     */
    public function getRoute()
    {
        $route = $this->getOption('route', null);

        if (!$route) {
            return null;
        }

        if (is_string($route)) {
            $route = ['name' => $route];
        }

        if ($route === true) {
            $route = ['name' => true];
        }

        $route = array_merge(
            [
                'name' => true,
                'params' => [],
                'withGet' => false,
                'withDomain' => false,
                'method' => 'POST'
            ],
            (array)$route
        );

        if ($route['name'] === true) {
            $route['name'] = $this->getComponentName();
        }

        if (isset($route[0])) {
            throw new Error('Use deprecated init route. Define named options', $this);
        }

        $routeParams = [];

        $row = $this->get();

        foreach ((array)$route['params'] as $routeParamKey => $routeParamValue) {
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

        $route['params'] = $routeParams;

        return $route;
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

//            if ($this->event['class'][0] == '_') {
//                $this->event['class'] = $this->getWidgetClass() . $this->event['class'];
//            }

            $this->event['class'] = Action::getClass($this->event['class'], $this->getWidgetClass());
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
     * @return null
     */
    public function getParentWidgetId()
    {
        return $this->parentWidgetId;
    }

    /**
     * @return null
     */
    public function getParentWidgetClass()
    {
        return $this->parentWidgetClass;
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

    public function getMethod()
    {
        $route = $this->getRoute();

        return isset($route['method']) ? $route['method'] : 'POST';
    }

    public function getDataAction()
    {
        return Json::encode($this->getEvent());
    }

    /**
     * @return string
     */
    public function getDataParams()
    {
        return Json::encode($this->get());
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

    protected function getValidValue()
    {
        if ($this->getOption('value') === null && $this->getOption('valueKey') === null) {
            if ($route = $this->getRoute()) {
                return Resource::create(Route::getClass())->get($route['name'], $route['params']);
            }
        }

        return parent::getValidValue();
    }
}
