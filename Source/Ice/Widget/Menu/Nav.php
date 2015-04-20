<?php
namespace Ice\Widget\Menu;

use Ice\Core\Widget_Menu;
use Ice\View\Render\Php;

class Nav extends Widget_Menu
{
    /**
     * @return Nav
     */
    public static function create()
    {
        return parent::create();
    }

    /**
     * @param $name
     * @param $title
     * @param Nav $nav
     * @param array $options
     * @param string $template
     * @return Nav
     */
    public function nav($name, $title, Nav $nav, array $options = [], $template = 'Nav')
    {
        $options['nav'] = $nav;
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

            $items[] = Php::getInstance()->fetch($menuClass . '_' . $item['template'], $item);
        }

        return Php::getInstance()->fetch(
            Widget_Menu::getClass($menuClass),
            [
                'items' => $items,
                'menuName' => $menuName,
                'classes' => $this->getClasses(),
                'style' => $this->getStyle()
            ]
        );
    }
}
