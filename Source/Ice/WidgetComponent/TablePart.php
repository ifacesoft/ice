<?php

namespace Ice\WidgetComponent;

use DateTime;
use DateTimeZone;
use Ice\Core\Loader;
use Ice\Core\Render;
use Ice\Core\Widget as Core_Widget;
use Ice\Render\Replace;

class TablePart extends HtmlTag
{
    private $valueTemplate = null;
    private $value = null;

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

        $this->setValue($options, $widget);
    }

    /**
     * @param array $row
     * @param Core_Widget $widget
     * @return mixed
     */
    public function build(array $row, Core_Widget $widget)
    {
        /** @var TablePart $tablePart */
        $tablePart = parent::build($row, $widget);

        return $tablePart
            ->buildValueTemplate($this->getParams())
            ->buildValue($this->getParams());
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

    private function setValue($options, $widget)
    {
        $this->value = isset($options['value']) ? $options['value'] : null;

        if ($this->value === null) {
            $this->value = array_key_exists('default', $this->getOption())
                ? $this->getOption('default')
                : $this->getComponentName();
        }
    }

    private function buildValueTemplate($params)
    {
        $this->valueTemplate = $this->getOption('valueTemplate');

        if ($this->valueTemplate === true) {
            $this->valueTemplate = $this->getComponentName();
        }

        if ($this->valueTemplate) {
            if ($resource = $this->getResource()){
                $this->valueTemplate = $resource->get($this->valueTemplate, $params);
            }
        }

        return $this;

    }

    /**
     * @return null
     */
    public function getValueTemplate()
    {
        return $this->valueTemplate;
    }

    private function buildValue($params)
    {
        if ($dataFormat = $this->getOption('dateFormat')) {
            $date = $this->getOption('dateTimezone')
                ? new DateTime($params[$this->value], new DateTimeZone($this->getOption('dateTimezone') ?: 'Europe/Moscow'))
                : new DateTime($params[$this->value]);

            $params[$this->value] = $date->format($this->getOption('dateFormat'));
        }

        if ($valueTemplate = $this->getValueTemplate()) {
            if ($render = strstr($valueTemplate, '/', true)) {
                $renderClass = Render::getClass($render);

                if (Loader::load($renderClass, false)) {
                    $valueTemplate = substr($valueTemplate, strlen($render) + 1);
                } else {
                    $renderClass = Replace::getClass();
                }
            } else {
                $renderClass = Replace::getClass();
            }

            $this->setLabel($renderClass::getInstance()->fetch($valueTemplate, $params, null, Render::TEMPLATE_TYPE_STRING));
        } else {
            if ($resource = $this->getResource()) {
                $this->setLabel($resource->get($this->value, $params));
            }
        }

        return $this;
    }
}