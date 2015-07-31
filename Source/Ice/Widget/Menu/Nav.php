<?php
namespace Ice\Widget\Menu;

use Ice\Core\Widget_Menu;
use Ice\View\Render\Php;

class Nav extends Widget_Menu
{
    protected $navClasses = null;

    protected static function config()
    {
        return [
            'view' => ['template' => null, 'viewRenderClass' => null, 'layout' => null],
            'input' => [],
            'access' => ['roles' => [], 'request' => null, 'env' => null]
        ];
    }

    /**
     * @param $url
     * @param $action
     * @param null $block
     * @param array $data
     * @return Nav
     */
    public static function create($url, $action, $block = null, array $data = [])
    {
        return parent::create($url, $action, $block, $data);
    }

    /**
     * @param $name
     * @param $title
     * @param array $options
     * @param string $template
     * @return Nav
     */
    public function nav($name, $title, array $options = [], $template = 'Ice\Widget\Menu\Nav_Nav')
    {
        return $this->addPart($name, $title, $options, $template, 'Nav_Subnav');
    }

//    public function render()
//    {
//        /**
//         * @var Nav $menuClass
//         */
//        $menuClass = get_class($this);
//        $menuName = 'Menu_' . $menuClass::getClassName();
//
//        $items = [];
//
//        foreach ($this->getItems() as $itemName => $item) {
//            $item['name'] = $itemName;
//
//            $templateBaseClass = $item['template'][0] == '_' ? $menuClass : Widget_Menu::getClass();
//
//            $items[] = Php::getInstance()->fetch($templateBaseClass . '_' . $item['template'], $item);
//        }
//
//        return Php::getInstance()->fetch(
//            Widget_Menu::getClass($menuClass),
//            [
//                'items' => $items,
//                'menuName' => $menuName,
//                'classes' => $this->getClasses(),
//                'style' => $this->getStyle(),
//                'navClasses' => $this->getNavClasses()
//            ]
//        );
//    }

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
