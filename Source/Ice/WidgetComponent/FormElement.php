<?php

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;
use Ice\Core\Request;
use Ice\Core\Widget as Core_Widget;
use Ice\DataProvider\Router;
use Ice\Helper\Input;

class FormElement extends HtmlTag
{
    private $validators = null;
    private $name = null;
    private $value = null;
    private $horizontal = null;

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

        $this->setName();
        $this->setValue();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    private function setName()
    {
        $this->name = $this->getOption('name') ? $this->getOption('name') : $this->getComponentName();
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return array_key_exists($this->value, $this->getParams())
            ? htmlentities($this->getParams()[$this->value], ENT_QUOTES)
            : '';
    }


    private function setValue()
    {
        $this->value = $this->getOption('value') ? $this->getOption('value') : null;

        if ($this->value === null) {
            $this->value = array_key_exists('default', $this->getOption())
                ? $this->getOption('default')
                : $this->getComponentName();
        }
    }

    protected function initParams(Core_Widget $widget)
    {
        parent::initParams($widget);

//        $config = ['providers' => $this->getOption('providers') ?: ['default', Request::class]];
//
//        if ($default = $this->getOption('default')) {
//            $config['default'] = $default;
//        }
//
//        $input = Input::get([$this->getValue() => $config]);
//
//        $this->params[$this->getName()] = $input[$this->getValue()];
    }


    public function build(array $row, Core_Widget $widget)
    {
        /** @var FormElement $component */
        $component = parent::build($row, $widget);

        return $component
            ->buildValidators()
            ->buildHorizontal($widget);
    }


    protected function buildParams($values)
    {
        parent::buildParams($values);

        $this->params[$this->name] = $this->value == $this->getComponentName()
            ? (array_key_exists($this->value, $values) ? $values[$this->value] : null)
            : (array_key_exists($this->value, $values) ? $values[$this->value] : $this->value);
    }


    private function buildHorizontal(Core_Widget $widget)
    {
        $this->setHorizontal($widget->getOption('horizontal'));

        return $this;
    }

    /**
     * @return null
     */
    public function getHorizontal()
    {
        return $this->horizontal;
    }

    /**
     * @param null $horizontal
     */
    public function setHorizontal($horizontal)
    {
        $this->horizontal = $horizontal;
    }

    /**
     * @return null
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * @param null $validators
     */
    protected function setValidators($validators)
    {
        $this->validators = $validators;
    }

    private function buildValidators()
    {
        $this->setValidators($this->getOption('validators'));
        return $this;
    }
}