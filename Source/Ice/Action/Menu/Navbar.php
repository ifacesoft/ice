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
 *
 * @version 0.1
 * @since 0.1
 */
class Menu_Navbar extends Action
{
    /**  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standard|file
     *      'viewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'viewRenderClassName' => 'Ice:Php',
        'inputValidators' => [
            'menu' => 'Ice:Is_Menu'
        ],
        'layout' => 'div.Menu_Navbar.collapse.navbar-collapse'
    ];

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