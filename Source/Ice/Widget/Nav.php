<?php

namespace Ice\Widget;

use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Widget;

abstract class Nav extends Widget
{
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
     * @return Nav
     */
    public function nav($name, array $options = [], $template = 'Ice\Widget\Nav\Nav')
    {
        $options['widget']->setClasses($options['widget']->getClasses() . ' nav-nav');

        return $this->widget($name, $options, $template);
    }
}