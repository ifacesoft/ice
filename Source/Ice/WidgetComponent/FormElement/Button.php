<?php

namespace Ice\WidgetComponent;

class FormElement_Button extends FormElement
{
    public function __construct($name, array $options, $template, $componentName)
    {
        parent::__construct($name, $options, $template, $componentName);

        if (isset($options['submit'])) {
            $this->submitPartName = $name;
        }
    }
}