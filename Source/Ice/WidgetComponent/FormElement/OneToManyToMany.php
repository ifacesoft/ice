<?php

namespace Ice\WidgetComponent;

class FormElement_OneToManyToMany extends FormElement
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
        if (isset($part['options']['oneToManyToMany'])) {
            /** @var Model $linkModelClass1 */
            /** @var Model $linkModelClass2 */
            list($linkModelClass1, $linkFieldName, $linkModelClass2) = $part['options']['oneToManyToMany'];
            $model = $linkModelClass1::getModel($values[$part['name']], $linkFieldName);
            $model = $model ? $model->fetchOne($linkModelClass2, $part['value'], true) : null;
            $values[$part['value']] = $model ? $model->get($part['value'], false) : '&nbsp;';
        }
    }
}