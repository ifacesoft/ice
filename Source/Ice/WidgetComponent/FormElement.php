<?php

namespace Ice\WidgetComponent;

use DateTime;
use DateTimeZone;
use Ice\Core\Loader;
use Ice\Core\Render;
use Ice\Core\Widget as Core_Widget;
use Ice\Helper\Json;
use Ice\Render\Replace;

class FormElement extends HtmlTag
{
    private $validators = null;
    private $name = null;
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

    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($componentName, $options, $template, $widget);

        $this->setName($options);
        $this->setValue($options);

        if (!empty($options['validators'])) {
            $this->validators = $options['validators'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    private function setName($options)
    {
        $this->name = isset($options['name']) ? $options['name'] : $this->getComponentName();
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return array_key_exists($this->value, $this->getParams())
            ? htmlentities($this->getParams()[$this->value], ENT_QUOTES)
            : '';
    }


    private function setValue($options)
    {
        $this->value = isset($options['value']) ? $options['value'] : null;

        if ($this->value === null) {
            $this->value = array_key_exists('default', $this->getOption())
                ? $this->getOption('default')
                : $this->getComponentName();
        }
    }

    protected function buildParams($values)
    {
        parent::buildParams($values);

        $params = [];

        $params[$this->name] = $this->value == $this->getComponentName()
            ? (array_key_exists($this->value, $values) ? $values[$this->value] : null)
            : (array_key_exists($this->value, $values) ? $values[$this->value] : $this->value);
        
        return array_merge(parent::buildParams($values), $params);
    }
}