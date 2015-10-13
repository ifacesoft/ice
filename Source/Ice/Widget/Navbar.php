<?php

namespace Ice\Widget;

use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Widget;

abstract class Navbar extends Widget
{
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
        $options['widget']->setClasses($options['widget']->getClasses() . ' navbar-nav');

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
        $options['widget']->setClasses($options['widget']->getClasses() . ' navbar-form');

        return $template
            ? $this->widget($name, $options, $template)
            : $this->widget($name, $options);
    }
}