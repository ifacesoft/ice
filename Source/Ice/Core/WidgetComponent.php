<?php

namespace Ice\Core;

use Ice\Helper\Access;
use Ice\Render\Replace;

abstract class WidgetComponent
{
    private $componentName = null;
    private $templateClass = null;
    private $resourceClass = null;
    private $renderClass = null;
    private $label = null;
    private $offset = null;
    private $widgetId = null;
    private $partId = null;

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

        $this->widgetId = $widget->getWidgetId();
        $this->partId = $this->widgetId . '_' . $componentName;
        
        $this->templateClass = $template;
        $this->setTemplateClass($options, $widget);
        $this->setResourceClass($options, $widget);
        $this->setRenderClass($options, $widget);
        $this->setLabel($options, $widget);

        if (isset($options['default']) && $this->getValue($name) === null) {
            $this->bind([$name => $options['default']]);
        }

        // Todo: Это используется
        //        $part['title'] = isset($part['options']['title']) ? $part['options']['title'] : $name;
//        unset($part['options']['title']);
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

    public function setResourceClass(array $options, Widget $widget)
    {
        if (empty($options['resource'])) {
            $resource = $widget->getResource();

            $this->resourceClass = $resource ? $resource->getResourceClass() : null;
        } else {
            $this->resourceClass = $options['resource'];
        }

        if ($this->resourceClass instanceof Resource) {
            $this->resourceClass = $this->resourceClass->getResourceClass();
        }

        /** @var Widget $widgetClass */
        $widgetClass = get_class($widget);

        if ($this->resourceClass === true) {
            $this->resourceClass = $widgetClass::getClassName();
        }
    }

    /**
     * @return string
     */
    public function getTemplateClass()
    {
        return $this->templateClass;
    }

    private function setTemplateClass($options, $widget)
    {
//        if (!empty($options['template'])) {
//            $this->templateClass = $options['template'];
//        }

        /** @var Widget $widgetComponentClass */
        $widgetComponentClass = get_class($this);

        if (empty($this->templateClass) || $this->templateClass === true) {
            return $this->templateClass = $widgetComponentClass;
        }

        if ($this->templateClass[0] == '_') {
            return $this->templateClass = $widgetComponentClass . $this->templateClass;
        }

        return $this->templateClass;
    }

    /**
     * @return Render
     */
    public function getRender()
    {
        $renderClass = Render::getClass($this->renderClass);

        return $renderClass::getInstance();
    }

    /**
     * @param $options
     * @param $widget
     * @return Render
     */
    public function setRenderClass($options, $widget)
    {
        if (!empty($options['resource'])) {
            $this->renderClass = $options['render'];
        }

        if (empty($this->renderClass) || $this->renderClass === true) {
            $this->renderClass = Config::getInstance(Render::getClass())->get('default');
        }
    }

    private function setLabel($options, $widget)
    {
        if (isset($options['template'])) {
            if ($options['template'] === true) {
                $options['template'] = $this->componentName;
            }

            if ($resource = $this->getResource()) {
                $options['template'] = $resource->get($options['template']/*, $resourceParams*/);
            }

            if ($render = strstr($options['template'], '/', true)) {
                $renderClass = Render::getClass($render);
                if (Loader::load($renderClass, false)) {
                    $options['template'] = substr($options['template'], strlen($render) + 1);
                } else {
                    $renderClass = Replace::getClass();
                }
            } else {
                $renderClass = Replace::getClass();
            }

            $this->label =
                $renderClass::getInstance()->fetch(
                    $options['template'],
                    []/*$part['params']*/,
                    null,
                    Render::TEMPLATE_TYPE_STRING
                );

        } else {
            $this->label = isset($options['label']) ? $options['label'] : $this->componentName;

            if ($this->label && $resource = $this->getResource()) {
                $this->label = $resource->get($this->label/*, $resourceParams*/);
            }
        }
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
     * @return null
     */
    public function getLabel()
    {
        return $this->label;
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


}