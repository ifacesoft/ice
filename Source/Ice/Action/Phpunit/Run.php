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
            'view' => ['template' => ''],
            'input' => [
                'default' => [
                    'vendor' => ['default' => 'phpunit/phpunit'],
                    'script' => ['default' => 'phpunit']
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