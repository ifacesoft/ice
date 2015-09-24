<?php

namespace Ice\Widget;

use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Widget;

class Navbar extends Widget
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [],
            'output' => []
        ];
    }

    /**
     * Init widget parts and other
     * @param array $input
     * @return array|void
     */
    public function init(array $input)
    {
    }

    /**
     * @param $name
     * @param array $options
     * @param string $template
     * @return Navbar
     */
    public function brand($name, array $options = [], $template = 'Ice\Widget\Navbar\Brand')
    {
        return $this->addPart($name, $options, $template, __FUNCTION__);
    }

    /**
     * @param string $name
     * @param array $options
     * @param string $template
     * @return Navbar
     */
    public function nav($name, array $options = [], $template = null)
    {
        $classes = $options['widget']->getClasses();
        $options['widget']->setClasses($classes . ' navbar-nav');

        return $template
            ? $this->widget($name, $options, $template)
            : $this->widget($name, $options);
    }

    /**
     * @param string $name
     * @param array $options
     * @param string $template
     * @return Navbar
     */
    public function form($name, array $options = [], $template = null)
    {
        $classes = $options['widget']->getClasses();
        $options['widget']->setClasses($classes . ' navbar-form');

        return $template
            ? $this->widget($name, $options, $template)
            : $this->widget($name, $options);
    }
}