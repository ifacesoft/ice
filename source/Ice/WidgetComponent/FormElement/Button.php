<?php

namespace Ice\WidgetComponent;

use Ice\Core\QueryBuilder;
use Ice\Widget\Form;
use Ice\Core\Widget as Core_Widget;

class FormElement_Button extends FormElement
{
    private $buttonType = 'button';

    /**
     * FormElement_Button constructor.
     * @param $componentName
     * @param array $options
     * @param $template
     * @param Form $widget // todo: Должен быть именно Form (Widget временный костыль)
     * @throws \Ice\Core\Exception
     */
    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($componentName, $options, $template, $widget);

        if (isset($options['submit'])) {
            $widget->setSubmitComponentName($componentName);
            $options['buttonType'] = 'submit';
        }

        if (isset($options['buttonType'])) {
            $this->buttonType = $options['buttonType'];
        }
    }

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

    /**
     * @return string
     */
    public function getButtonType()
    {
        return $this->buttonType;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function filter(QueryBuilder $queryBuilder)
    {
        return $queryBuilder;
    }

    protected function getClasses($classes = '')
    {
        return 'btn ' . parent::getClasses($classes);
    }
}