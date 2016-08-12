<?php

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;
use Ice\Core\Model;
use Ice\Core\QueryBuilder;
use Ice\Core\Widget as Core_Widget;
use Ice\Helper\Date;

class FormElement extends HtmlTag // todo: должен быть абстракт
{
    private $name = null;
    private $horizontal = null;

    public function __construct($componentName, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($componentName, $options, $template, $widget);

        $this->horizontal = $widget->getOption('horizontal', 0);
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
     * @return null
     */
    public function getHorizontal()
    {
        return $this->horizontal;
    }

//    public function build(array $row)
//    {
//        /** @var FormElement $component */
//        $component = parent::build($row);
//
//        return $component->buildValidators();
//    }

//    protected function buildParams(array $values)
//    {
//        parent::buildParams($values);
//
//        $name = $this->getName();
//
//        if ($this->get($name, null) === null) {
//            $this->set($name, $this->getFromProviders($this->getName(), $values));
//        }
//
////        if (!isset($this->params[$name])) {
////            $this->params[$name] = $this->value == $this->getComponentName()
////                ? (array_key_exists($this->value, $values) ? $values[$this->value] : null)
////                : (array_key_exists($this->value, $values) ? $values[$this->value] : $this->value);
////        }
//    }

    public function save(Model $model)
    {
        if ($this->getOption('readonly', false) || $this->getOption('disabled', false)) {
            return [];
        }

        $value = $this->getValue();

        if ($dateFormat = $this->getOption('dateFormat')) {
            $value = Date::get(strtotime($value), Date::FORMAT_MYSQL);
        }

        return [$this->getName() => is_array($value) ? $value : html_entity_decode($value)];
    }
//
//    /**
//     * @return null
//     */
//    public function getValidators()
//    {
//        return $this->validators;
//    }
//
//    /**
//     * @param null $validators
//     */
//    protected function setValidators($validators)
//    {
//        $this->validators = $validators;
//    }

//    private function buildValidators()
//    {
//        $this->setValidators($this->getOption('validators'));
//        return $this;
//    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name = $this->getOption('name') ? $this->getOption('name') : $this->getComponentName();
    }

    public function filter(QueryBuilder $queryBuilder)
    {
        $option = array_merge($this->getOption(), $this->getOption('filter', []));

        $value = $this->getValue();

        if ($value === null || $value === '' || (is_array($value)) && empty($value)) {
            return;
        }

        if (!isset($option['comparison'])) {
            $option['comparison'] = 'like';
        }

        if (!isset($option['modelClass'])) {
            $option['modelClass'] = $queryBuilder->getModelClass();
        }

        foreach ((array)$value as $val) {
            $val = html_entity_decode($val);

            switch ($option['comparison']) {
                case '=':
                    $queryBuilder->eq([$this->getName() => $val], $option['modelClass']);
                    break;
                case 'like':
                default:
                    $queryBuilder->like($this->getName(), '%' . $val . '%', $option['modelClass']);
            }
        }
    }

    protected function getValidValue()
    {
        $valueKey = $this->getValueKey();

        $value = $this->getOption('value', []);

        if ($value && !is_array($value)) {
            return $value;
            $valueKey = $value;
        }

        $defaultValueKey = is_array($value) && array_key_exists('default', $value)
            ? $value['default']
            : '';

        return $this->get($valueKey, $defaultValueKey);
    }
}