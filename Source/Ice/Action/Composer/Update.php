<?php
/**
 * Ice action composer update class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Helper\Console;

/**
 * Class Composer_Update
 *
 * Updates vendor projects via composer
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 *
 * @version 0.0
 * @since   0.0
 */
class Composer_Update extends Action
{
    /**
     * Action config
     *
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    protected static function config()
    {
        return [
            'view' => ['template' => ''],
            'input' => [],
            'access' => [
                'roles' => [],
                'request' => 'cli',
                'env' => null
            ],
            'cache' => ['ttl' => -1, 'count' => 1000],
        ];
    }

    /**
     * Run action
     *
     * @param  array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function run(array $input)
    {
        $composerPharFile = MODULE_DIR . 'composer.phar';

        $commands = [
            'php ' . $composerPharFile . ' self-update',
            'php ' . $composerPharFile . ' clear-cache',
            'php ' . $composerPharFile . ' update --prefer-source --optimize-autoloader',
            'php ' . $composerPharFile . ' show -i'
        ];

        if (!file_exists($composerPharFile)) {
            array_unshift($commands, 'curl -sS https://getcomposer.org/installer | php');
        }

        Console::run($commands);

        Console::getText('For composer update use command "./cli Ice:Deploy" or directly "./cli Ice:Composer_Update"', Console::C_YELLOW);
    }
}
