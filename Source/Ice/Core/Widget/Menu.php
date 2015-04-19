<?php

namespace Ice\Core;

use Ice\Core;

abstract class Widget_Menu extends Widget
{
    protected $items = null;

    /**
     * Return Widget_Menu items
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
     */
    public function getItems()
    {
        return (array)$this->items;
    }

    public function link($name, $title, array $options = [], $template = 'Link')
    {
        return $this->addItem($name, $title, $options, $template);
    }

    /**
     * Add menu item
     *
     * @param  $name
     * @param  $title
     * @param  $options
     * @param  $template
     * @return Widget_Menu
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.1
     */
    protected function addItem($name, $title, array $options, $template)
    {
        if (!isset($options['disable']) || !$options['disable']) {
            $this->items[$name] = [
                'title' => $title,
                'options' => $options,
                'template' => $template
            ];
        }

        return $this;
    }

    public function button($name, $title, array $options = [], $template = 'Button')
    {
        return $this->addItem($name, $title, $options, $template);
    }

    public function bind($key, $value)
    {
        $this->addValue($key, $value);

        return $value;
    }
}
