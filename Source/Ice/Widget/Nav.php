<?php

namespace Ice\Widget;

use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Widget;

class Nav extends Widget
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
     * @param string $name
     * @param array $options
     * @param string $template
     * @return Navbar
     */
    public function nav($name, array $options = [], $template = 'Ice\Widget\Nav\Nav')
    {
        $classes = $options['widget']->getClasses();
        $options['widget']->setClasses($classes . ' nav-nav');

        return $template
            ? $this->widget($name, $options, $template)
            : $this->widget($name, $options);
    }

    protected function build(array $input)
    {
    }
}