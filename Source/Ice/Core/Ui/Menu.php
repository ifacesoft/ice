<?php

namespace Ice\Core;

use Ice\Core;

abstract class Ui_Menu extends Container
{
    use Ui, Stored;

    private $items = null;
    private $_key = null;

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
     * Create new instance of menu
     *
     * @param $key
     * @return Ui_Menu
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.1
     */
    protected static function create($key)
    {
        $class = self::getClass();
//
//        if ($key) {
//            $class .= '_' . $key;
//        }

        $menu = new $class();

        $menu->_key = $key;

        return $menu;
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

    /**
     * @param null $name
     * @return array
     */
    public function getKey($name = null)
    {
        if ($name) {
            return isset($this->_key[$name]) ? $this->_key[$name] : null;
        }

        return $this->_key;
    }
}