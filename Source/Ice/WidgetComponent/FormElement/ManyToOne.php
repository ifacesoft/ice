<?php

namespace Ice\WidgetComponent;

class FormElement_ManyToOne extends FormElement
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
    
    public function initValues() {
        if (isset($part['options']['manyToOne']) && empty($part['options']['rows'])) {
            /** @var Model $linkModelClass1 */
            list($linkFieldName, $linkModelClass1) = $part['options']['manyToOne'];

            $values[$part['value']] = $linkModelClass1::createQueryBuilder()
                ->eq([$linkFieldName => $values[$part['name']]])
                ->group($linkFieldName)
                ->func(['GROUP_CONCAT' => $part['value']], '"",' . $part['value'])
                ->getSelectQuery('/pk')
                ->getValue($part['value']);
        }
    }
}