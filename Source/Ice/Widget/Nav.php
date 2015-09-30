<?php

namespace Ice\Widget;

use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Widget;

abstract class Nav extends Widget
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [],
            'output' => []
        ];
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Nav
     */
    public function li($name, array $options = [], $template = 'Ice\Widget\Nav\Li')
    {
        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Nav
     */
    public function dropdown($name, array $options = [], $template = 'Ice\Widget\Nav\Dropdown')
    {
        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    /**
     * @param Nav $widget
     * @param array $options
     * @param string $template
     * @return Navbar
     */
    public function nav(Nav $widget, array $options = [], $template = 'Ice\Widget\Nav\Nav')
    {
        $widget->setClasses($widget->getClasses() . ' nav-nav');

        return $this->widget($widget, $options, $template);
    }
}