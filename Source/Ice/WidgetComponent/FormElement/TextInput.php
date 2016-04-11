<?php

namespace Ice\WidgetComponent;

use Ice\Core\Widget as Core_Widget;

class FormElement_TextInput extends FormElement
{
    /**
     * @var string
     */
    private $placeholder = null;

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
    
    public function __construct($name, array $options, $template, $componentName)
    {
        parent::__construct($name, $options, $template, $componentName);
    }

    public function build(array $row, Core_Widget $widget)
    {
        /** @var FormElement_TextInput $component */
        $component = parent::build($row, $widget);

        return $component
            ->buildPlaceholder($row, $widget);
    }

    private function buildPlaceholder($row, Core_Widget $widget)
    {
        $this->placeholder = $this->getOption('placeholder');
        
        if ($this->placeholder) {
            if ($resource = $this->getResource()) {
                $this->placeholder = $resource->get($this->placeholder);
            }
        }

        return $this;
    }

    /**
     * @param string $attributeName
     * @return string
     */
    public function getPlaceholderAttribute($attributeName = 'placeholder')
    {
        return $this->placeholder ? ' ' . $attributeName . '="' . $this->placeholder . '"' : '';
    }
}