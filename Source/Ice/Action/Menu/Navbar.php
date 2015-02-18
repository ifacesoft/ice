<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Menu;
use Ice\View\Render\Php;

/**
 * Class Menu_Navbar
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context;
 *
 * @package Ice\Action;
 *
 * @author dp <denis.a.shestakov@gmail.com>
 */
class Menu_Navbar extends Action
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
            'view' => ['viewRenderClass' => 'Ice:Php', 'layout' => 'div.Menu_Navbar.collapse.navbar-collapse'],
            'input' => [
                'default' => [
                    'menu' => ['validators' => 'Ice:Is_Menu']
                ]
            ]
        ];
    }

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        $result = $input['menu']->getItems();

        foreach ($result as $position => &$items) {
            foreach ($items as &$item) {
                $item = Php::getInstance()->fetch(Menu::getClass($item['template']), $item);
            }
        }

        return ['items' => $result];
    }
}