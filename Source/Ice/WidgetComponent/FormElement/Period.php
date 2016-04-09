<?php

namespace Ice\WidgetComponent;

class FormElement_Period extends FormElement
{
    public function __construct($name, array $options, $template, $componentName)
    {
        parent::__construct($name, $options, $template, $componentName);

        $fields = [$name . '_from', $name . '_to'];

        if (!empty($options['default'])) {
            foreach ($fields as $name) {
                if ($this->getValue($name) === null) {
                    $this->bind([$name => $options['default'][$name]]);
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