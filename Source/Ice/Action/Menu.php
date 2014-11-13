<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Logger;
use Ice\View\Render\Php;
use Ice\Core\Menu as Core_Menu;

/**
 * Class Menu
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context;
 * @package Ice\Action;
 * @author dp
 * @version stable_0
 */
class Menu extends Action
{
    /**  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standart|file
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
            'scheme' => 'Ice:Not_Empty'
        ]

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
        $menu = '';

        $viewRender = Php::getInstance();

        foreach ($input['scheme'] as $title => $url) {
            if (is_array($url)) {
                $menu .= $viewRender->fetch(Core_Menu::getClass() . '_dropdown', ['title' => $title, 'dropdown' => $url]);
                continue;
            }

            $menu .= $viewRender->fetch(Core_Menu::getClass() . '_link', ['title' => $title, 'url' => $url]);
        }



        return ['menu' => $menu];
    }
}