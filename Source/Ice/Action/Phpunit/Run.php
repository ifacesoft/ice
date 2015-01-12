<?php
/**
 * Unin test run action
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Module;
use Ice\Helper\Console;

/**
 * Class Phpunit_Run
 *
 * Default ice form action
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since 0.0
 */
class Phpunit_Run extends Action
{

    /**
     *  public static $config = [
     *      'afterActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standard|file
     *      'defaultViewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'template' => '',
//        'beforeActions' => 'Ice:Composer_Update'
    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        $modulePath = Module::getInstance()->getPath();

        foreach (Module::getPathes() as $path) {
            $command = VENDOR_DIR . $input['vendor'] . '/' . $input['script'] .
                ' --configuration ' . $path . 'Config/vendor/phpunit.xml' .
                ' --bootstrap ' . $path . 'bootstrap.php';

            if ($path == $modulePath) {
                $command .= ' --coverage-clover=' . $path . 'Var/vendor/phpunit/coverage.xml';
            }

            Console::run($command . ' ' . $path . 'Test');
        }
    }
}