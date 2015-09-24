<?php

namespace Ice\Core;

use Ice\Helper\Object;

trait Rendered
{
    use Configured;

    /**
     * @param bool|true $layout
     * @param string $attributes
     * @param bool|false $force
     * @return string|null
     */
    public static function getLayout($layout, $attributes = '', $force = false) {
        /** @var Configured $class */
        $class = get_called_class();

        if (!$layout && !$force) {
            $layout = $class::getConfig()->get('render/layout');
        }

        if ($layout === true) {
            return 'div.' . Object::getClassName($class) . $attributes;
        }

        if (!$layout) {
            return null;
        }

        Debuger::dump($layout);

        if ($layout[0] == '_') {
            return 'div.' . Object::getClassName($class) . $layout . $attributes;
        }

        return $layout;
    }

    /**
     * @param bool|true $template
     * @param bool|false $force
     * @return string|null
     */
    public static function getTemplate($template, $force = false) {
        /** @var Configured $class */
        $class = get_called_class();

        if (!$template && !$force) {
            $template = $class::getConfig()->get('render/template');
        }

        if ($template === true) {
            return $class;
        }

        if (!$template) {
            return null;
        }

        if ($template[0] == '_') {
            return $class . $template;
        }

        return $template;
    }


    /**
     * @param bool|true $render
     * @param bool|false $force
     * @return Render
     */
    public static function getRender($render, $force = false) {
        /** @var Configured $class */
        $class = get_called_class();

        if (!$render && !$force) {
            $render = $class::getConfig()->get('render/class');
        }

        if (!$render || $render === true) {
            $render = Config::getInstance(Render::getClass())->get('default');
        }

        /** @var Render $renderClass */
        $renderClass = Render::getClass($render);

        return $renderClass::getInstance();
    }
}