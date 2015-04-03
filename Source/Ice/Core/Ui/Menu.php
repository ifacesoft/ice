<?php

namespace Ice\Core;

use Ice\Core;

abstract class Ui_Menu extends Container
{
    use Ui;

    private $items = null;

    private function __construct()
    {
    }

    /**
     * Return instance of Ui_Menu
     *
     * @param null $key
     * @param null $ttl
     * @return Ui_Menu
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

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
}