<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Exception\Not_Show;
use Ice\Exception\Not_Valid;
use Ice\Helper\Date;
use Ice\Helper\Input;
use Ice\Helper\Type_String;
use Ice\Render\Replace;
use Ice\Widget\Block;
use Ice\WidgetComponent\HtmlTag;
use Ice\WidgetComponent\Widget as Component_Widget;

abstract class WidgetComponent
{
    use Core;
    use Configured;

    private static $ids = [];
    protected $label = null;
    private $options = [];
    private $componentName = null;
    private $templateClass = null;
    private $resourceClass = null;
    private $renderClass = null;
    private $offset = 0;
    private $widgetClass = null;
    private $widgetId = null;
    private $value = null;
    private $active = null;
    private $classes = null;

    private $params = [];

    /**
     * WidgetComponent constructor.
     * @param $componentComponentName
     * @param array $options
     * @param $template
     * @param Widget $widget
     * @throws Exception
     */
    public function __construct($componentComponentName, array $options, $template, Widget $widget)
    {
//        if (isset($options['roles'])) {
//            if (!isset($options['access'])) {
//                $options['access'] = [];
//            }
//
//            $options['access']['roles'] = $options['roles'];
//            unset($options['roles']);
//        }
//
//        if (!empty($options['access'])) {
//            Access::check($options['access']);
//        }

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

        $this->widgetClass = get_class($widget);
        $this->widgetId = $widget->getWidgetId();
    }

    /**
     * @param $renderClass
     * @return $this
     * @throws Exception
     * @throws Config_Error
     * @throws FileNotFound
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
     * @param $resourceClass
     * @param Widget|null $widget
     * @return $this
     * @throws Config_Error
     * @throws Exception
     * @throws FileNotFound
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
     * @param $templateClass
     * @return $this
     * @throws Exception
     */
    protected function setTemplateClass($templateClass)
    {
        /** @var Widget $widgetComponentClass */
        $widgetComponentClass = get_class($this);

        $this->templateClass = $templateClass === null
            ? $widgetComponentClass::getConfig()->get('render/template', true)
            : $templateClass;

        if ($this->templateClass && is_string($this->templateClass) && $this->templateClass[0] == '_') {
            $this->templateClass = $widgetComponentClass . $this->templateClass;
        }

        if (empty($this->templateClass) || $this->templateClass === true) {
            $this->templateClass = $widgetComponentClass;
        }

        return $this;
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

    public function set(array $params)
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    public function getId($postfix = '')
    {
        if ($postfix) {
            $postfix = '_' . $postfix;
        }

        $id = $this->getWidgetComponentId() . $postfix;

        return isset(self::$ids[$id]) ? self::$ids[$id] : self::$ids[$id] = 'wc_' . crc32($id);
    }

    /**
     * @return null|string
     */
    public function getWidgetComponentId()
    {
        return $this->getWidgetId() . '_' . $this->getComponentName() . '_' . $this->getOffset();
    }

    /**
     * @return null|string
     */
    public function getWidgetId()
    {
        return $this->widgetId;
    }

    /**
     * @return null
     */
    public function getComponentName()
    {
        return $this->componentName;
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

    public function cloneComponent($offset)
    {
        $widgetComponent = clone $this;
        $widgetComponent->setOffset($offset);

        return $widgetComponent;
    }

    /**
     * @return null
     * @throws Config_Error
     * @throws Exception
     * @throws FileNotFound
     * @throws Error
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
            $this->setLabel($resource->get($this->label, $this->get()));
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
     * @return Resource|Resource|null
     * @throws FileNotFound
     */
    public function getResource()
    {
        if (!$this->resourceClass) {
            return null;
        }

        return Resource::create($this->resourceClass);
    }

    /**
     * @param null $paramName
     * @param null $default
     * @param bool $withWidgetParams
     * @return array|int|mixed|null|string
     * @throws Exception
     * @throws Config_Error
     * @throws Error
     * @throws FileNotFound
     */
    public function get($paramName = null, $default = null, $withWidgetParams = true)
    {
        /** @var Widget $widgetClass */
        $widgetClass = $this->getWidgetClass();

        $params = $withWidgetParams
            ? array_merge(
                $widgetClass::getRegistry($this->getWidgetId())->get(),
                $this->params
            )
            : $this->params;

        $paramConfig = [];

        foreach ($this->getParamConfig() as $param => $value) {
            if (is_array($value)) {
                $paramConfig[$param] = $value;
            } else {
                $params[$param] = $value;
            }
        }

        if (!empty($paramConfig)) {
            $params = array_merge($params, Input::get($this->getParamConfig(), $params, get_class($this)));
        }

        if ($paramName === null) {
            return empty($params) ? [] : $params;
        }

        $param = array_key_exists($paramName, $params) ? $params[$paramName] : $default;

        // todo: реализовать через фильтры
        $dateFormat = $this->getOption('dateFormat');

        if ($param && $dateFormat) {
            if ($dateFormat === true) {
                $dateDefaults = Module::getInstance()->getDefault('date');
                $dateFormat = $dateDefaults->get('format');
            }

            $param = Date::get($param, $dateFormat, null);

//            $this->setOption('dateFormat', null); //todo: С этим должно что-то сделать - нужно предотвратить повторное использование
        }

        return $param;
    }

    /**
     * @return null|string
     */
    public function getWidgetClass()
    {
        return $this->widgetClass;
    }

    protected function getParamConfig($paramName = null)
    {
        $paramsConfig = [];

        foreach ((array)$this->getOption('params', []) as $param => $config) {
            if (is_int($param)) {
                $param = $config;
                $config = [];
            }

            if ($paramName !== null && $paramName != $param) {
                continue;
            }

            $paramsConfig[$param] = $config;
        }

        return $paramsConfig;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }
//
//    /**
//     * @return array
//     */
//    public function getParams()
//    {
//        return $this->params;
//    }

    public function mergeOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @param string $classes
     * @return null
     */
    public function getClassAttribute($classes = '')
    {
        return 'class="' . $this->getClasses($classes) . '"';
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

    public function getIdAttribute($postfix = '')
    {
        return 'id="' . $this->getId($postfix) . '" data-for="' . $this->getWidgetId() . '"';
    }

    public function getAll($paramName = null, $default = null)
    {
        /** @var Widget $widgetClass */
        $widgetClass = $this->getWidgetClass();

        $params = array_merge($widgetClass::getRegistry($this->getWidgetId())->get(), $this->params);

        if ($this instanceof Component_Widget) {
            $params = array_merge($this->getWidget()->getAll(), $params);
        }

        if ($paramName === null) {
            return empty($params) ? [] : $params;
        }

        return array_key_exists($paramName, $params) ? $params[$paramName] : $default;
    }

    public function ifOption($name, $value, $default = null)
    {
        return $this->getOption($name, $default) === $value;
    }

    /**
     * @param bool $encode
     * @return null
     * @throws Exception
     * @throws Config_Error
     * @throws Error
     * @throws FileNotFound
     */
    public function getValue($encode = null)
    {
        $value = $this->getValidValue();

        /**
         * 0 is too value
         */
        if ($value === null || $value === '' || $value === '&nbsp;') {
            return $value;
        }

        $resourceClass = $this->getOption('valueResource', null);

        if ($resourceClass === null) {
            $resourceClass = $this->getOption('valueHardResource', null);
        }

        $resource = null;

        if ($resourceClass) {
            if ($resourceClass === true) {
                $resource = $this->getResource();
            }

            if (!$resource) {
                $resource = Resource::create($resourceClass);
            }
        }

        $resourceKey = $this->getOption('valueKey') ? $this->getValueKey() : $value;
        $resourceParams = array_merge($this->get(), [$this->getValueKey() => $value]);

        if ($resource) {
            if ($this->getOption('valueHardResource')) {
                $resourceKey = $this->getValueKey() . '_' . $value;
            }

            $value = $resource->get($resourceKey, $resourceParams);
        } else {
            $value = Replace::getInstance()->fetch($value, $resourceParams, null, Render::TEMPLATE_TYPE_STRING);
        }

        $dateFormat = $this->getOption('dateFormat');

        if ($value && $dateFormat) {
            if ($dateFormat === true) {
                $dateDefaults = Module::getInstance()->getDefault('date');
                $dateFormat = $dateDefaults->get('format');
            }

            $value = Date::get($value, $dateFormat, null);
        }

        if ($value && $truncate = $this->getOption('truncate')) {
            $value = Type_String::truncate($value, $truncate);
        }

        if ($encode === null) {
            $encode = $this->getOption('encode', true);
        }

        return $encode && !is_array($value) ? htmlentities($value) : $value;
    }

    /**
     * @return array|int|mixed|string|null
     * @throws Config_Error
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     */
    protected function getValidValue()
    {
        $this->validate();

        $valueKey = $this->getValueKey();

        $value = $this->getOption('value', []);

        if ($value && !is_array($value)) {
            if ($value === true) {
                $valueKey = $this->getComponentName();
            } else {
                return $value;
            }
        }

        $defaultValueKey = is_array($value) && array_key_exists('default', $value)
            ? $value['default']
            : $valueKey;

        return $this->get($valueKey, $defaultValueKey);
    }

    /**
     * @param array $params
     * @return array
     * @throws Config_Error
     * @throws Error
     * @throws Exception
     * @throws FileNotFound
     */
    public function validate(array $params = [])
    {
        return Validator::validateParams(array_merge($params, $this->get()), (array)$this->getOption('params', []));
    }

    public function getValueKey()
    {
        $valueKey = $this->getOption('valueKey', true);

        if ($valueKey === true) {
            $valueKey = $this->getComponentName();
        }

        return $valueKey;
    }

    public function render(Render $render = null)
    {
        if ($render === null) {
            $render = $this->getRender();
        }

        if ($this->getOption('render', true) === false) {
            return '';
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
     * @return string
     */
    public function getTemplateClass()
    {
        return $this->templateClass;
    }

    public function getLayout()
    {
        return $this->getOption('wrap');
    }

    /**
     * @param Not_Valid $e
     * @return string
     * @throws Exception
     */
    protected function getNotValidResult(Not_Valid $e)
    {
        $error = new HtmlTag(
            'error',
            ['value' => $e->getMessage()],
            'Ice\WidgetComponent\Bootstrap_Alert_Danger',
            Block::getInstance(crc32($e->getMessage()))
        );

        return $error->render();
    }

    public function join(QueryBuilder $queryBuilder) {
        return $queryBuilder;
    }
}