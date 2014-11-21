<?php

namespace Ice\Core;

use Ice\Core;

abstract class Menu extends Container
{
    use Core;

    private $items = [];
    private $key = null;

    private function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Return instance of Menu
     *
     * @param null $key
     * @param null $ttl
     * @return Menu
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
     * @param null $hash
     * @return Menu
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected static function create($key, $hash = null)
    {
        /** @var Menu $class */
        $class = self::getClass();

        if ($class == __CLASS__) {
            $class = 'Ice\Menu\\' . $key;
        }

        return new $class($key);
    }

    /**
     * Add menu item
     *
     * @param $itemType
     * @param $title
     * @param $options
     * @param $position
     * @param $isActive
     * @param $template
     * @return Menu
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected function addItem($itemType, $title, $options, $position, $isActive, $template)
    {
        if ($isActive) {
            $this->items[$position][] = [
                'itemType' => $itemType,
                'title' => $title,
                'options' => $options,
                'template' => $template
            ];
        }
        return $this;
    }

    /**
     * Return Menu items
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
}