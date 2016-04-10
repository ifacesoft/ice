<?php

namespace Ice\Core;

use Ice\Helper\Access;
use Ice\Render\Replace;

abstract class WidgetComponent
{
    use Configured;

    private $options = [];

    private $componentName = null;
    private $templateClass = null;
    private $resourceClass = null;
    private $renderClass = null;
    private $offset = null;
    private $widgetId = null;
    private $partId = null;
    private $label = null;
    private $labelTemplate = null;
    private $params = [];

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
     * @param $componentName
     * @param array $options
     * @param $template
     * @param Widget $widget
     */
    public function __construct($componentName, array $options, $template, Widget $widget)
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

        $this->componentName = $componentName;
        $this->options = $options;
        
        if (isset($this->options['template'])) { // todo: когда-нибудь убрать этот костыль
            $this->options['labelTemplate'] = $this->options['template'];
            unset($this->options['template']);
        }
        $this->options['template'] = $template;

        $this->widgetId = $widget->getWidgetId();
        $this->partId = $this->widgetId . '_' . $componentName;

        // Todo: Это используется
        //        $part['title'] = isset($part['options']['title']) ? $part['options']['title'] : $name;
//        unset($part['options']['title']);
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
        return $this->partId;
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
     * @param Widget $widget
     * @return $this
     */
    public function build(array $row, Widget $widget)
    {
        $this->params = $this->getParams($row);

        return $this
            ->setTemplateClass($this->getOption('template'))
            ->setResourceClass($this->getOption('resource'), $widget)
            ->setRenderClass($this->getOption('render'))
            ->buildLabelTemplate($this->params)
            ->buildLabel($this->params);
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
            $this->resourceClass = $widgetComponentClass::getClassName();
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

        $renderClass = Render::getClass($this->renderClass);

        return $renderClass::getInstance();
    }

    /**
     * @param $renderClass
     * @return $this
     */
    private function setRenderClass($renderClass)
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

//    private function partParams($partName, WidgetComponent $part, array $values)
    protected function buildParams($values)
    {
        $this->params = [
            $this->getComponentName() => array_key_exists($this->getComponentName(), $values) 
                ? $values[$this->getComponentName()] 
                : null
        ];

        $params = (array)$this->getOption('params');

            foreach ($params as $key => $value) {
                if (is_int($key)) {
                    $key = $value;
                }

                if (is_string($value)) {
                    $this->params[$key] = $key == $value
                        ? (array_key_exists($value, $values) ? $values[$value] : null)
                        : (array_key_exists($value, $values) ? $values[$value] : $value); //(isset($part['options']['default']) ? $part['options']['default'] : $value)
                } else {
                    $this->params[$key] = $value;
                }
            }
    }
    
    private function buildLabelTemplate($params)// todo: пусть сам label уже будет labelTemplate
    {
        $this->labelTemplate = $this->getOption('labelTemplate');

        if ($this->labelTemplate === true) {
            $this->labelTemplate = $this->getComponentName();
        }

        if ($this->labelTemplate && $resource = $this->getResource()) {
            $this->labelTemplate = $resource->get($this->labelTemplate, $params);
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getLabelTemplate()
    {
        return $this->labelTemplate;
    }


    private function buildLabel($row)
    {
        $this->setLabel($this->getOption('label'));

        if (!$this->getLabel()) {
            $this->setLabel($this->getComponentName());
        }

        if ($labelTemplate = $this->getLabelTemplate()) {
            if ($render = strstr($labelTemplate, '/', true)) {
                $renderClass = Render::getClass($render);

                if (Loader::load($renderClass, false)) {
                    $labelTemplate = substr($labelTemplate, strlen($render) + 1);
                } else {
                    $renderClass = Replace::getClass();
                }
            } else {
                $renderClass = Replace::getClass();
            }

            $this->setLabel($renderClass::getInstance()->fetch($labelTemplate, $row, null, Render::TEMPLATE_TYPE_STRING));
        } else {
            if ($resource = $this->getResource()) {
                $this->setLabel($resource->get($this->label, $row));
            }
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param null $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }


    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
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
}