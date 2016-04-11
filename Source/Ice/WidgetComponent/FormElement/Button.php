<?php

namespace Ice\WidgetComponent;

use Ice\Widget\Form;

class FormElement_Button extends FormElement
{
    private $buttonType = 'button';

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

    public function __construct($componentName, array $options, $template, Form $widget)
    {
        parent::__construct($componentName, $options, $template, $widget);

        if (isset($options['submit'])) {
            $widget->setSubmitComponentName($componentName);
            $this->buttonType = 'submit';
        }
    }

    /**
     * @return string
     */
    public function getButtonType()
    {
        return $this->buttonType;
    }
}