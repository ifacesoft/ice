<?php

namespace Ice\Widget\Menu;

use Ice\Core\Widget_Menu;

class Navbar extends Widget_Menu
{
    /**
     * @return Navbar
     */
    public static function create()
    {
        return parent::create();
    }

    /**
     * Add menu dropdown item
     *
     * @param  $name
     * @param  $title
     * @param  array $options
     * @param  string $template
     * @return Navbar
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.1
     */
    public function dropdown($name, $title, array $options = [], $template = 'Dropdown')
    {
        return $this->addItem($name, $title, $options, $template);
    }

    public function render()
    {
        // TODO: Implement render() method.
    }
}
