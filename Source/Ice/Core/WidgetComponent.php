<?php

namespace Ice\Core;

use Ice\Action\PhpUnit;
use Ice\Core;
use Ice\Exception\Error;
use Ice\Exception\Not_Show;
use Ice\Exception\Not_Valid;
use Ice\Helper\Access;
use Ice\Helper\Date;
use Ice\Helper\Input;
use Ice\Helper\String;
use Ice\Render\Replace;
use Ice\Widget\Block;
use Ice\WidgetComponent\HtmlTag;

abstract class WidgetComponent
{
    use Core;
    use Configured;

    private $options = [];
    private $componentName = null;
    private $templateClass = null;
    private $resourceClass = null;
    private $renderClass = null;
    private $offset = null;

    private $widgetId = null;

    private $value = null;
    protected $label = null;

    protected $params = [];

    private $active = null;
    private $classes = null;
    private $id = [];

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
     * WidgetComponent constructor.
     * @param $componentComponentName
     * @param array $options
     * @param $template
     * @param Widget $widget
     */
    public function __construct($componentComponentName, array $options, $template, Widget $widget)
    {
        if (isset($options['roles'])) {
            if (!isset($options['access'])) {
                $options['access'] = [];
            }

            $options['access']['roles'] = $options['roles'];
            unset($options['roles']);
        }

        if (!empty($options['access'])) {
            Access::check($options['access']);
        }

        $this->componentName = $componentComponentName;
        $this->options = $options;

        if (isset($this->options['template'])) { // todo: когда-нибудь убрать этот костыль
            $this->options['labelTemplate'] = $this->options['template'];
            unset($this->options['template']);
        }
        $this->options['template'] = $template;

        $this
            ->setTemplateClass($this->getOption('template'))
            ->setResourceClass($this->getOption('resource'), $widget)
            ->setRenderClass($this->getOption('render'));

        $this->widgetId = $widget->getWidgetId();
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return null
     */
    public function getComponentName()
    {
        return $this->componentName;
    }

    /**
     * @return null|string
     */
    public function getWidgetId()
    {
        return $this->widgetId;
    }

    /**
     * @return null|string
     */
    public function getPartId()
    {
        return $this->getWidgetId() . '_' . $this->getComponentName();
    }

    /**
     * @param $param
     * @param array $options
     * @param null $default
     */
    protected function init($param, array $options, $default = null)
    {
        $this->setOption($param, array_key_exists($param, $options) ? $options[$param] : $default);
    }

    /**
     * @param array $row
     * @return $this
     */
    public function build(array $row)
    {
        $this->params = [];

        $this->buildParams($row);

        return $this;
    }

    public function cloneComponent()
    {
        return clone $this;
    }

    /**
     * @return string
     */
    public function getTemplateClass()
    {
        return $this->templateClass;
    }

    /**
     * @param $templateClass
     * @return $this
     */
    private function setTemplateClass($templateClass)
    {
        /** @var Widget $widgetComponentClass */
        $widgetComponentClass = get_class($this);

        $this->templateClass = $templateClass === null
            ? $widgetComponentClass::getConfig()->get('render/template', true)
            : $templateClass;

        if ($this->templateClass[0] == '_') {
            $this->templateClass = $widgetComponentClass . $this->templateClass;
        }

        if (empty($this->templateClass) || $this->templateClass === true) {
            $this->templateClass = $widgetComponentClass;
        }

        return $this;
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        if (!$this->resourceClass) {
            return null;
        }

        return Resource::create($this->resourceClass);
    }

    /**
     * @param $resourceClass
     * @param Widget|null $widget
     * @return $this
     */
    private function setResourceClass($resourceClass, Widget $widget = null)
    {
        /** @var Widget $widgetComponentClass */
        $widgetComponentClass = get_class($this);

        $this->resourceClass = $resourceClass === null
            ? $widgetComponentClass::getConfig()->get('render/resource', null)
            : $resourceClass;

        if ($this->resourceClass === null && $widget) {
            $resource = $widget->getResource();

            $this->resourceClass = $resource ? $resource->getResourceClass() : null;
        }

        if ($this->resourceClass instanceof Resource) {
            $this->resourceClass = $this->resourceClass->getResourceClass();
        }

        if ($this->resourceClass === true) {
            $this->resourceClass = get_class($widget);
        }

        if ($this->resourceClass === false) {
            $this->resourceClass = null;
        }

        return $this;
    }

    /**
     * @return Render
     */
    public function getRender()
    {
        if (!$this->renderClass) {
            return null;
        }

        /** @var Render $renderClass */
        $renderClass = Render::getClass($this->renderClass);

        return $renderClass::getInstance();
    }

    /**
     * @param $renderClass
     * @return $this
     */
    public function setRenderClass($renderClass)
    {
        /** @var Widget $widgetComponentClass */
        $widgetComponentClass = get_class($this);

        $this->renderClass = $renderClass === null
            ? $widgetComponentClass::getConfig()->get('render/class', null)
            : $renderClass;

        if ($this->renderClass instanceof Render) {
            $this->renderClass = get_class($this->renderClass);
        }

        if ($this->renderClass === true) {
            $this->renderClass = Config::getInstance(Render::getClass())->get('default');
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getLabel()
    {
        if ($this->label !== null) {
            return $this->label;
        }

        $this->setLabel($this->getOption('label', true));

        if ($this->label === true) {
            $this->setLabel($this->getComponentName());
        }

        /** @var Resource $resource */
        if ($resource = $this->getResource()) {
            $this->setLabel($resource->get($this->label, $this->getParams()));
        }

        return $this->label;
    }

    /**
     * @param null $label
     * @return null
     */
    public function setLabel($label)
    {
        return $this->label = $label;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        if ($this->active !== null) {
            return $this->active;
        }

        return $this->setActive((bool)$this->getOption('active', false));
    }

    /**
     * @param bool|null $active
     * @return bool|null
     */
    protected function setActive($active)
    {
        return $this->active = $active;
    }

    public function mergeOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getOption($name = null, $default = null)
    {
        if ($name === null) {
            return $this->options;
        }

        return array_key_exists($name, $this->options) ? $this->options[$name] : $default;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    protected function getClasses($classes = '')
    {
        if ($this->classes !== null) {
            return $this->classes;
        }

        $class = (array)$this->getOption('classes', []);


        if ($class) {
            $classes .= ' ' . implode(' ', $class);
        }

        if ($this->isActive()) {
            $classes .= ' active';
        }

        return $this->classes = $this->getComponentName() . ' ' . $classes;
    }

    /**
     * @param string $classes
     * @return null
     */
    public function getClassAttribute($classes = '')
    {
        return 'class="' . $this->getClasses($classes) . '"';
    }

    public function getId($postfix = '')
    {
        if (isset($this->id[$postfix])) {
            return $this->id[$postfix];
        }

        if ($postfix) {
            $postfix = '_' . $postfix;
        }

        return $this->id[$postfix] = $this->getPartId() . '_' . $this->getOffset() . $postfix;
    }

    public function getIdAttribute($postfix = '')
    {
        return 'id="' . $this->getId($postfix) . '" data-for="' . $this->getWidgetId() . '"';
    }

    public function get($param = null, $default = null)
    {
        if ($param === null) {
            return $this->params;
        }

        if (isset($this->params[$param])) {
            return $this->params[$param];
        }

        $params = Input::get($this->getParamsConfig());

        return isset($params[$param]) ? $params[$param] : $default;
    }

    public function set(array $params)
    {
        foreach ($params as $param => $value) {
            $this->params[$param] = $value;
        }

        return $this;
    }

    public function ifOption($name, $value, $default = null)
    {
        return $this->getOption($name, $default) === $value;
    }

    protected function getValidValue()
    {
        $this->validate();
        return $this->get($this->getValueKey());
    }

    /**
     * @param bool $encode
     * @return null
     */
    public function getValue($encode = null)
    {
        $value = $this->getValidValue();

        if (empty($value)) {
            return $value;
        }

        $resourceClass = $this->getOption('valueResource', null);

        if ($resourceClass === null) {
            $resourceClass = $this->getOption('valueHardResource', null);
        }

        if ($resourceClass) {
            $resource = $resourceClass === true
                ? $this->getResource()
                : Resource::create($resourceClass);

            $value = $resource->get($value, $this->getParams());

            if ($this->getOption('valueHardResource')) {
                $value = $resource->get($this->getValueKey() . '_' . $value, $this->getParams());
            }
        } else {
            $value = Replace::getInstance()->fetch($value, $this->getParams(), null, Render::TEMPLATE_TYPE_STRING);
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

        if ($encode === null) {
            $encode = $this->getOption('encode', true);
        }

        return $encode && !is_array($value) ? htmlentities($value) : $value;
    }

    public function getValueKey()
    {
        $valueKey = $this->getOption('valueKey', true);

        if ($valueKey === true) {
            $valueKey = $this->getComponentName();
        }

        return $valueKey;
    }

    protected function getParamsConfig()
    {
        $valueKey = $this->getValueKey();
        $value = $this->getOption('value');

        if ($value === null && isset($this->params[$valueKey])) {
            $value = $this->params[$valueKey];
        }

        $paramsConfig = (array)$this->getOption('params', []);

        if (!isset($paramsConfig[$valueKey])) {
            $paramsConfig[$valueKey] = [];
        }

        if ($value !== null) {
            $paramsConfig[$valueKey]['default'] = $value;
        }

        return $paramsConfig;
    }

    protected function buildParams(array $values)
    {
        $this->set(Input::get($this->getParamsConfig(), $values));

        $valueKey = $this->getValueKey();

        if (empty($this->get($valueKey))) {
            return;
        }

        $dateFormat = $this->getOption('dateFormat');

        if ($dateFormat === true) {
            $dateDefaults = Module::getInstance()->getDefault('date');
            $dateFormat = $dateDefaults->get('format');
        }

        if ($dateFormat) {
            $this->set($valueKey, Date::get(strtotime($this->get($valueKey)), $dateFormat));
            $this->setOption('dateFormat', null);
        }
    }

    public function render(Render $render = null)
    {
        if ($render === null) {
            $render = $this->getRender();
        }

        try {
            $result = $render->fetch(
                $this->getTemplateClass(),
                ['component' => $this, 'render' => $render],
                $this->getLayout()
            );
        } catch (Not_Show $e) {
            $result = '';
        } catch (Not_Valid $e) {
            $result = $this->getNotValidResult($e);
        }

        return $result;
    }

    protected function getNotValidResult(Not_Valid $e)
    {
        $error = new HtmlTag('error', ['value' => $e->getMessage()], 'Ice\WidgetComponent\Bootstrap_Alert_Danger', Block::getInstance(null));

        return $error->build([])->render();
    }

    public function getLayout()
    {
        return null; // todo: dummy - need implement
    }

    public function validate()
    {
        return Validator::validateParams($this->getParams(), (array)$this->getOption('params', []));
    }
}