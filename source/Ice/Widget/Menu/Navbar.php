<?php

namespace Ice\Widget\Menu;

use Ice\Core\Widget_Menu;
use Ice\Render\Php;

class Navbar extends Widget_Menu
{
    /**
     * @var string
     */
    private $brand = null;

    /**
     * @param $url
     * @param $action
     * @param null $block
     * @param null $event
     * @return Navbar
     */
    public static function create($url, $action, $block = null, $event = null)
    {
        return parent::create($url, $action, $block, $event);
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
        /**
         * @var Nav $menuClass
         */
        $menuClass = get_class($this);
        $menuName = 'Menu_' . $menuClass::getClassName();

        $items = [];

        foreach ($this->getItems() as $itemName => $item) {
            $item['name'] = $itemName;
            $position = isset($item['options']['position'])
                ? $item['options']['position']
                : '';

            $items[$position][] = Php::getInstance()->fetch($menuClass . '_' . $item['template'], $item);
        }

        return Php::getInstance()->fetch(
            Widget_Menu::getClass($menuClass),
            [
                'items' => $items,
                'menuName' => $menuName,
                'classes' => $this->getClasses(),
                'style' => $this->getStyle(),
                'brand' => $this->getBrand()
            ]
        );
    }

    /**
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     * @return Navbar
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }
}
