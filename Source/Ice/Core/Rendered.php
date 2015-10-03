<?php

namespace Ice\Core;

use Ice\Helper\Object;

trait Rendered
{
    use Configured;

    private $resource = null;

    /**
     * @param string $attributes
     * @param bool|false $force
     * @return string|null
     */
    public static function getLayout($attributes = '', $force = false)
    {
        $layout = null;

        /** @var Configured $class */
        $class = get_called_class();

        if (!$force) {
            $layout = $class::getConfig()->get('render/layout');
        }

        if (!$layout) {
            return null;
        }

        if ($layout === true) {
            return 'div.' . Object::getClassName($class) . $attributes;
        }

        if ($layout[0] == '_') {
            return 'div.' . Object::getClassName($class) . $layout . $attributes;
        }

        return $layout;
    }

    /**
     * @param bool|false $force
     * @return string|null
     */
    public static function getTemplate($force = false)
    {
        $template = null;

        /** @var Configured $class */
        $class = get_called_class();

        if (!$force) {
            $template = $class::getConfig()->get('render/template');
        }

        if (!$template) {
            return null;
        }

        if ($template === true) {
            return $class;
        }

        if ($template[0] == '_') {
            return $class . $template;
        }

        return $template;
    }


    /**
     * @param bool|false $force
     * @return Render
     */
    public static function getRender($force = false)
    {
        $render = null;

        /** @var Configured $class */
        $class = get_called_class();

        if (!$force) {
            $render = $class::getConfig()->get('render/class');
        }

        if (!$render || $render === true) {
            $render = Config::getInstance(Render::getClass())->get('default');
        }

        /** @var Render $renderClass */
        $renderClass = Render::getClass($render);

        return $renderClass::getInstance();
    }

    public function setResource($resource, $force = false)
    {
        if ($resource instanceof Resource) {
            return $this->resource = $resource;
        }

        /** @var Configured $class */
        $class = get_called_class();

        if (!$resource && !$force) {
            $resource = $class::getConfig()->get('render/resource');
        }

        if (!$resource) {
            return null;
        }

        if ($resource === true || (is_array($resource) && !isset($resource['class']))) {
            $resource = $class;
        }

        if (is_array($resource)) {
            $resource = $resource['class'];
        }

        return $this->resource = Resource::create($resource);
    }

    /**
     * @param null $resource
     * @param bool|false $force
     * @return Resource
     */
    protected function getResource($resource = null, $force = false)
    {
        if (!$force && $this->resource !== null) {
            return $this->resource;
        }

        return $this->setResource($resource);
    }
}