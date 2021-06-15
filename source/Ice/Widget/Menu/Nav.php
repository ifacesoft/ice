<?php
namespace Ice\Widget\Menu;

use Ice\Core\Widget_Menu;
use Ice\Render\Php;

class Nav extends Widget_Menu
{
    private $navClasses = null;

    /**
     * @param $url
     * @param $action
     * @param null $block
     * @param null $event
     * @return Nav
     */
    public static function create($url, $action, $block = null, $event = null)
    {
        return parent::create($url, $action, $block, $event);
    }

    /**
     * @param $name
     * @param $title
     * @param Widget_Menu $nav
     * @param array $options
     * @param string $template
     * @return Nav
     */
    public function nav($name, $title, Widget_Menu $nav, array $options = [], $template = 'Nav')
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
                'style' => $this->getStyle(),
                'navClasses' => $this->getNavClasses()
            ]
        );
    }

    /**
     * @return string
     */
    public function getNavClasses()
    {
        return $this->navClasses;
    }

    /**
     * @param string $navClasses
     * @return Nav
     */
    public function setNavClasses($navClasses)
    {
        $this->navClasses = $navClasses;
        return $this;
    }
}
