<?php

namespace Ice\WidgetComponent;

use Ice\Core\Widget as Core_Widget;

class FormElement_Period extends FormElement_TextInput
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

    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($componentName, $options, $template, $widget);

        $fields = [$componentName . '_from', $componentName . '_to'];

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