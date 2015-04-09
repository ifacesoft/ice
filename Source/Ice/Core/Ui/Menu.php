<?php

namespace Ice\Core;

use Ice\Core;

abstract class Ui_Menu extends Ui
{
    private $items = [];

    /**
     * Return Ui_Menu items
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add menu item
     *
     * @param $name
     * @param $title
     * @param $options
     * @param $template
     * @return Ui_Menu
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.1
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

    public function link($name, $title, array $options = [], $template = 'Link')
    {
        return $this->addItem($name, $title, $options, $template);
    }

    public function bind($key, $value)
    {
        $this->addValue($key, $value);

        return $value;
    }
}