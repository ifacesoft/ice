<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 19.04.16
 * Time: 11:27
 */

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;
use Ice\Core\Module;
use Ice\Core\QueryBuilder;
use Ice\Helper\Date;
use Ice\Helper\String;

class ValueElement extends HtmlTag
{
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

    /**
     * @param bool $encode
     * @return null
     */
    public function getValue($encode = true)
    {
        $value = $this->get($this->getRawValue());

        if ($value === null || $value === '') {
            return $value;
        }

        $resourceClass = $this->getOption('valueResource', null);

        if ($resourceClass === null) {
            $resourceClass = $this->getOption('valueHardResource', null);
        }

        /** @var Resource $resource */
        $resource = $resourceClass === true
            ? $this->getResource()
            : ($resourceClass === null ? $resourceClass : $resourceClass::create());

        $template = null;

        if ($resource) {
            $template = $this->getOption('valueHardResource', null)
                ? $this->getComponentName() . '_' . $value
                : $this->getRawValue();
        }

        if ($template) {
            $value = $resource->get($template, $this->getParams());
        }

        if ($dateFormat = $this->getOption('dateFormat')) {
            if ($dateFormat === true) {
                $dateDefaults = Module::getInstance()->getDefault('date');
                $dateFormat = $dateDefaults->get('format');
            }

            $value = Date::get(strtotime($this->value), $dateFormat);
        }

        if ($truncate = $this->getOption('truncate')) {
            $value = String::truncate($value, $truncate);
        }



        return $encode ? htmlentities($value) : $value;
    }

    protected function getRawValue()
    {
        if ($this->value !== null) {
            return $this->value;
        }

        $value = $this->getOption('value', true);

        if ($value === true) {
            $value = $this->getComponentName();
        }

        return $this->setValue($value);
    }

    private function setValue($value)
    {
        return $this->value = $value;
    }

    protected function buildParams($values)
    {
        parent::buildParams($values);

        $value = $this->getRawValue();

        if (!isset($this->params[$value])) {
            $this->params[$value] = $value == $this->getComponentName()
                ? (array_key_exists($value, $values) ? $values[$value] : null)
                : (array_key_exists($value, $values) ? $values[$value] : $value);
        }

        if (!isset($this->params[$value])) {
            if ($default = $this->getOption('default')) {
                $this->params[$value] = $default;
            }
        }

        $dateFormat = $this->getOption('dateFormat');

        if ($dateFormat === true) {
            $dateDefaults = Module::getInstance()->getDefault('date');
            $dateFormat = $dateDefaults->get('format');
        }

        if ($dateFormat) {
            if (isset($this->params[$value])) {
                $this->params[$value] = Date::get(strtotime($this->params[$value]), $dateFormat);
                $this->setOption('dateFormat', null);
            }
        }
    }
}