<?php

namespace Ice\WidgetComponent;

use Ice\Core\Widget as Core_Widget;
use Ice\Helper\Json;

class FormElement extends HtmlTag
{
    private $validators = null;
    private $name = null;
    private $value = null;
    private $params = [];

    public function __construct($name, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($name, $options, $template, $widget);

        $this->setName($options, $widget);
        $this->setValue($options, $widget);

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

    private function setName($options, $widget)
    {
        $this->name = isset($options['name']) ? $options['name'] : $this->getComponentName();
    }


    /**
     * @return null
     */
    public function getValue()
    {
        return array_key_exists($this->value, $this->params) ? htmlentities($this->params[$this->value], ENT_QUOTES) : '';
    }


    private function setValue($options, $widget)
    {
        $this->value = isset($options['value']) ? $options['value'] : $this->getComponentName();
    }


    /**
     * @param array $options
     * @return mixed|string
     */
    protected function getPlaceholder(array $options)
    {
        $placeholder = empty($options['placeholder'])
            ? $this->getName() . '_placeholder'
            : $options['placeholder'];

        if ($resource = $this->getResource()) {
            $placeholder = $resource->get($placeholder);
        }

        return $placeholder;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return Json::encode($this->params);
    }
}