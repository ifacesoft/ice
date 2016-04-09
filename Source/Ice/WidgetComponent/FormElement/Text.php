<?php

namespace Ice\WidgetComponent;

class FormElement_Text extends FormElement
{
    /**
     * @var string
     */
    private $placeholder = null;

    public function __construct($name, array $options, $template, $componentName)
    {
        parent::__construct($name, $options, $template, $componentName);

        $this->placeholder = $this->getPlaceholder($options);
    }
}