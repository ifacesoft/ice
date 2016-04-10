<?php

namespace Ice\WidgetComponent;

class FormElement_Period extends FormElement
{
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

        $fields = [$name . '_from', $name . '_to'];

        if (!empty($options['default'])) {
            foreach ($fields as $paramName) {
                if ($this->getValue($paramName) === null) {
                    $this->bind([$paramName => $options['default'][$paramName]]);
                }
            }
        }

        if (empty($options['params'])) {
            $options['params'] = $fields;
        } else {
            $options['params'] += $fields;
        }
    }
}