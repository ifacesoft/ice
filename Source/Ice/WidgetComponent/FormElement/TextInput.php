<?php

namespace Ice\WidgetComponent;

class FormElement_TextInput extends FormElement
{
    /**
     * @param string $attributeName
     * @return string
     */
    public function getPlaceholderAttribute($attributeName = 'placeholder')
    {
        return $attributeName . '="' . $this->getPlaceholder() . '"';
    }

    public function getPlaceholder() {
        $placeholder = $this->getOption('placeholder', '');

        if (!$placeholder) {
            return ' ';
        }

        if ($placeholder === true) {
            $placeholder = $this->getComponentName() . '_placeholder';
        }

        if ($resource = $this->getResource()) {
            return $resource->get($placeholder);
        }

        return $placeholder;
    }
}