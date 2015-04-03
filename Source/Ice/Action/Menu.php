<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Ui_Menu;
use Ice\Helper\Json;
use Ice\View\Render\Php;

/**
 * Class Menu
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context;
 *
 * @package Ice\Action;
 *
 * @author dp <denis.a.shestakov@gmail.com>
 */
class Menu extends Action
{
    /**
     * Action config
     *
     * example:
     * ```php
     *  $config = [
     *      'actions' => [
     *          ['Ice:Title', ['title' => 'page title'], 'title'],
     *          ['Ice:Another_Action, ['param' => 'value']
     *      ],
     *      'view' => [
     *          'layout' => Emmet::PANEL_BODY,
     *          'template' => _Custom,
     *          'viewRenderClass' => Ice:Twig,
     *      ],
     *      'input' => [
     *          Request::DEFAULT_DATA_PROVIDER_KEY => [
     *              'paramFromGETorPOST => [
     *                  'default' => 'defaultValue',
     *                  'validators' => ['Ice:PATTERN => PATTERN::LETTERS_ONLY]
     *                  'type' => 'string'
     *              ]
     *          ]
     *      ],
     *      'output' => ['Ice:Resource/Ice\Action\Index'],
     *      'ttl' => 3600,
     *      'roles' => []
     *  ];
     * ```
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Php'],
            'input' => [
                'menu' => ['validators' => 'Ice:Is_Ui_Menu']
            ]
        ];
    }

    /**
     * Run action
     *
     * @param array $input
     * @return array
     */
    public function run(array $input)
    {
        /** @var Ui_Menu $menu */
        $menu = $input['menu'];

        /** @var Menu $menuClass */
        $menuClass = get_class($menu);
        $menuName = 'Menu_' . $menuClass::getClassName();

        $items = [];

        foreach ($menu->getItems() as $name => $item) {
            $page = isset($item['options']['page'])
                ? $item['options']['page'] : 0;

            $item['name'] = $name;
            $item['menuName'] = $menuName;
            $item['href'] = $menu->getUrl();
            $item['dataJson'] = Json::encode($menu->getParams());
            $item['dataAction'] = $menu->getAction();
            $item['dataBlock'] = $menu->getBlock();
            $item['onclick'] = 'Ice_Action_Menu.click($(this), ' . $page . '); return false;';

            $items[] = Php::getInstance()->fetch($menuClass . '_' . $item['template'], $item);
        }

        return [
            'menu' => Php::getInstance()->fetch(
                Ui_Menu::getClass($menuClass),
                [
                    'items' => $items,
                    'menuName' => $menuName,
                    'classes' => $menu->getClasses(),
                    'style' => $menu->getStyle()
                ]
            )
        ];
    }
}