<?php

namespace Ice\WidgetComponent;

use DateTime;
use DateTimeZone;
use Ice\Core\Loader;
use Ice\Core\Render;
use Ice\Core\Widget as Core_Widget;
use Ice\Helper\Json;
use Ice\Render\Replace;

class FormElement extends HtmlTag
{
    private $validators = null;
    private $name = null;
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
    
    public function __construct($name, array $options, $template, Core_Widget $widget)
    {
        parent::__construct($name, $options, $template, $widget);

        $this->setName($options, $widget);
        $this->setValue($options, $widget);

        if (isset($options['default']) && $this->getValue($name) === null) {
            $this->bind([$name => $options['default']]);
        }
        
        if (!empty($options['validators'])) {
            $this->validators = $options['validators'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    private function setName($options, $widget)
    {
        $this->name = isset($options['name']) ? $options['name'] : $this->getComponentName();
    }


    /**
     * @return null
     */
    public function getValue()
    {
        return array_key_exists($this->value, $this->params) ? htmlentities($this->params[$this->value], ENT_QUOTES) : '';
    }


    private function setValue($options, $widget)
    {
        $this->value = isset($options['value']) ? $options['value'] : $this->getComponentName();
    }

    /**
     * @param array $options
     * @return mixed|string
     */
    protected function getPlaceholder(array $options)
    {
        $placeholder = empty($options['placeholder'])
            ? $this->getName() . '_placeholder'
            : $options['placeholder'];

        if ($resource = $this->getResource()) {
            $placeholder = $resource->get($placeholder);
        }

        return $placeholder;
    }

    /**
     * @return array
     */
    public function getDataParams()
    {
        return Json::encode($this->getParams());
    }

    protected function buildParams($values)
    {
        parent::buildParams($values);

        $value = isset($values[$part['value']]) ? $values[$part['value']] : 0;
        $valueFieldName = $part['value'];


        if ($part['value'] == $partName) {
            $paramValue = array_key_exists($part['value'], $values) ? $values[$part['value']] : null;

            if ($paramValue === null && isset($part['options']['default'])) {
                $paramValue = $part['options']['default'];
            }

            $part['params'] = [$part['name'] => $paramValue];
        } else {
            $part['params'] = [
                $part['name'] => array_key_exists($part['value'], $values) ? $values[$part['value']] : $part['value'],
            ];
        }

        if (isset($part['options']['dateFormat'])) {
            $date = array_key_exists('dateTimezone', $part['options'])
                ? new DateTime($part['params'][$part['name']], new DateTimeZone($part['options']['dateTimezone'] ?: 'Europe/Moscow'))
                : new DateTime($part['params'][$part['name']]);

            $part['params'][$part['name']] = $date->format($part['options']['dateFormat']);

            unset($part['options']['dateFormat']);
        }
        
        if (isset($part['options']['valueTemplate'])) {
            if ($part['options']['valueTemplate'] === true) {
                $part['options']['valueTemplate'] = $partName;
            }

            if ($part['resource']) {
                $part['options']['valueTemplate'] = $part['resource']->get($part['options']['valueTemplate'], $part['params']);
            }

            if ($render = strstr($part['options']['valueTemplate'], '/', true)) {
                $renderClass = Render::getClass($render);
                if (Loader::load($renderClass, false)) {
                    $part['options']['valueTemplate'] = substr($part['options']['valueTemplate'], strlen($render) + 1);
                } else {
                    $renderClass = Replace::getClass();
                }
            } else {
                $renderClass = Replace::getClass();
            }

            $part['params'][$part['title']] =
                $renderClass::getInstance()->fetch(
                    $part['options']['valueTemplate'],
                    $part['params'],
                    null,
                    Render::TEMPLATE_TYPE_STRING
                );

            unset($part['options']['valueTemplate']);
        }
    }
}