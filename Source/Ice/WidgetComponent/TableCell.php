<?php

namespace Ice\WidgetComponent;

use DateTime;
use DateTimeZone;
use Ice\Core\Debuger;
use Ice\Core\Loader;
use Ice\Core\Module;
use Ice\Core\Render;
use Ice\Core\Resource;
use Ice\Core\Widget as Core_Widget;
use Ice\Helper\Date;
use Ice\Render\Replace;

abstract class TableCell extends HtmlTag
{
    private $value = null;

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

    /**
     * @return null
     */
    public function getValue()
    {
        if ($this->value === null) {
            $this->setValue($this->getOption('value', true));

            if ($this->value === true) {
                $this->setValue($this->getComponentName());
            }
        }

        $resourceClass = $this->getOption('valueResource', null);

        if ($resourceClass === null) {
            $resourceClass = $this->getOption('valueHardResource', null);
        }

        /** @var Resource $resource */
        $resource = $resourceClass === true
            ? $this->getResource()
            : ($resourceClass === null ? $resourceClass : $resourceClass::create());

        $params = $this->getParams();
        
        $dateFormat = $this->getOption('dateFormat', null);
        
        if ($dateFormat === true) {
            $dateDefaults = Module::getInstance()->getDefault('date');
            $dateFormat = $dateDefaults->get('format');
        }
        
        if ($dateFormat) {
            if (isset($params[$this->value])) {
                $params[$this->value] = Date::get(strtotime($params[$this->value]), $dateFormat);
                $dateFormat = null;
            }
        }
        
        $default = $this->getOption('default', null);
        $value = isset($params[$this->value]) ? $params[$this->value] : ($default === null ? $this->value : $default);

        $template = null; 
        
        if ($resource) {
            $template = $this->getOption('valueHardResource', null)
                ? $this->getComponentName() . '_' . $value
                : $this->value;
        }

        if ($template) {
            $value = $resource->get($template, $params);
        }
       
        if ($dateFormat) {
            $value = Date::get(strtotime($value), $dateFormat);
        }
        
        return $value;
    }

    private function setValue($value)
    {
        $this->value = $value;
    }
}