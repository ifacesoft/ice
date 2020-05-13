<?php

namespace Ice\WidgetComponent;

class FormElement_TextInput extends FormElement
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = 'Ice\Widget\Form\Text';

        return $config;
    }

    /**
     * @param string $attributeName
     * @param string $postfix
     * @return string
     */
    public function getPlaceholderAttribute($attributeName = 'placeholder', $postfix = '')
    {
        return $attributeName . '="' . $this->getPlaceholder($postfix) . '"';
    }

    public function getPlaceholder($postfix = '')
    {
        $placeholder = $this->getOption('placeholder', '');

        if (!$placeholder) {
            return ' ';
        }

        if ($placeholder === true) {
            $placeholder = $this->getComponentName() . '_placeholder';
        }

        if ($resource = $this->getResource()) {
            return $resource->get($placeholder . $postfix);
        }

        return $placeholder;
    }
}