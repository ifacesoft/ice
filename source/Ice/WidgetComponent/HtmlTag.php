<?php

namespace Ice\WidgetComponent;

use Ice\Action\Render;
use Ice\Core\Action;
use Ice\Core\Exception;
use Ice\Core\Request;
use Ice\Core\Resource;
use Ice\Core\Route;
use Ice\Core\Router;
use Ice\Core\Widget as Core_Widget;
use Ice\Core\WidgetComponent;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Helper\Json;
use Ice\Helper\Type_String;

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
                return $this->setActive(Type_String::startsWith(Request::uri(), $href));
            }
        }

        return parent::isActive();
    }

    /**
     * @return null
     * @throws Error
     * @throws Exception
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
                return Router::getInstance()->getUrl([$route['name'], $route['params'], $route['withGet'], $route['withDomain'], $route['replaceContext']]);
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
     * @throws Exception
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

        if ($route['name'] === null) {
            $route['name'] = Router::getInstance()->getName();
        }

        $route = array_merge(
            [
                'name' => true,
                'params' => [],
                'withGet' => false,
                'withDomain' => false,
                'replaceContext' => [],
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
                $routeParams[$routeParamValue] = !is_array($routeParamValue) && array_key_exists(strval($routeParamValue), $row)
                    ? $row[$routeParamValue]
                    : $routeParamValue;

                continue;
            }

            $routeParams[$routeParamKey] = !is_array($routeParamValue) && array_key_exists(strval($routeParamValue), $row)
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
            case 'onkeyup':
                $code .= 'onkeyup="';
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
        $event = array_intersect_key($this->getOption(), array_flip(['onclick', 'onchange', 'onkeyup', 'submit']));

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
                'dataCallback' => null,
                'callback' => null,
                'confirm_massage' => null,
                'code' => ''
            ];
        } else {
            $this->event = [
                'type' => $eventType,
                'class' => empty($event['action']) ? Render::class : $event['action'],
                'params' => empty($event['params']) ? [] : (array)$event['params'],
                'ajax' => array_key_exists('ajax', $event) ? (boolean)$event['ajax'] : true,
                'dataCallback' => empty($event['dataCallback']) ? null : $event['dataCallback'],
                'callback' => empty($event['callback']) ? null : $event['callback'],
                'confirm_message' => empty($event['confirm_message']) ? null : $event['confirm_message'],
                'code' => empty($event['code']) ? '' : $event['code'],
            ];

            if ($this->event['class'][0] === '_') {
                /** @var Widget $widgetClass */
                $widgetClass = $this->getWidgetClass();

                $widgetNamespace = $widgetClass::getClassNamespace();
                $widgetName = $widgetClass::getClassName();

                $this->event['class'] = str_replace('Widget', 'Action', $widgetNamespace) . '\\' . $widgetName . $this->event['class'];
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

    public function getEventCode($element = 'this')
    {
        $event = $this->getEvent();

        if (!$event) {
            return '';
        }

        if ($event['code']) {
            return $event['code'];
        }

        $code = 'Ice_Core_Widget.click($(' . $element . '), \'' . $this->getHref() . '\', \'' . $this->getMethod() . '\'';

        $code .= $event['callback']
            ? ', ' . $event['callback']
            : ', null';

        $code .= $event['confirm_message']
            ? ', \'' . $event['confirm_message'] . '\''
            : ', null';

        $code .= $event['dataCallback']
            ? ', ' . $event['dataCallback']
            : ', null';

        return $code . '); return false;';
    }

    public function getMethod()
    {
        $event = $this->getEvent();

        if (isset($event['ajax']) && $event['ajax'] === false) {
            return 'GET';
        }

        $route = $this->getRoute();

        return isset($route['method']) ? $route['method'] : 'POST';
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getDataAction()
    {
        $event = $this->getEvent();

        unset($event['code']);
        unset($event['dataCallback']);
        unset($event['callback']);
        unset($event['confirm_message']);

        return Json::encode($event);
    }

    /**
     * @return string
     * @throws Error
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
     */
    public function getDataParams()
    {
        $params = $this->get();

        unset($params['code']);
        unset($params['dataCallback']);
        unset($params['callback']);
        unset($params['confirm_message']);

        return base64_encode(Json::encode($params));
    }

    public function getHtmlTagAttributes()
    {
        $htmlTagAttributes = '';

        if ($style = $this->getOption('style', null)) {
            if ($htmlTagAttributes) {
                $htmlTagAttributes .= ' ';
            }

            $htmlTagAttributes .= 'style="' . $style . '"';
        }

        if ($title = $this->getOption('title', null)) {
            if ($htmlTagAttributes) {
                $htmlTagAttributes .= ' ';
            }

            $htmlTagAttributes .= 'title="' . $title . '"';
        }

        return $htmlTagAttributes;
    }

    protected function getValidValue()
    {
        if ($this->getOption('value') === null && $this->getOption('valueKey') === null) {
            if ($route = $this->getRoute()) { // todo: наверное это условие должно быть выше
                $routeOption = $this->getOption('route');

                return isset($routeOption['value']) && $routeOption['value'] == 'href'
                    ? $this->getHref()
                    : Resource::create(Route::getClass())->get($route['name'], $route['params']);
            }
        }

        return parent::getValidValue();
    }
}
