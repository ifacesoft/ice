<?php

namespace Ice\WidgetComponent;

class FormElement_Text extends FormElement
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

        $this->placeholder = $this->getPlaceholder($options);
    }
}