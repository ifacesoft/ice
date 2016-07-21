<?php
/**
 * Ice action resources class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;
use Ice\Core\Module;
use Ice\Helper\Directory;

/**
 * Class Title
 *
 * Action of generation js and css for includes into html tag head (<script.. and <link..)
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Action
 */
class Resource extends Action
{
    protected static function config()
    {
        return [
            'view' => ['template' => ''],
            'actions' => ['Ice:Resource_Css', 'Ice:Resource_Js'],
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
     * @version 0.6
     * @since   0.0
     */
    public function run(array $input)
    {
        $compiledResourceDir = getCompiledResourceDir();

        foreach (array_keys(Module::getAll()) as $name) {
            $modulePath = Module::getInstance($name)->get('path');

            if (file_exists($imgSource = $modulePath . 'Resource/img')) {
                Directory::copy($imgSource, Directory::get($compiledResourceDir . 'img'));
            }
            if (file_exists($fontSource = $modulePath . 'Resource/font')) {
                Directory::copy($fontSource, Directory::get($compiledResourceDir . 'font'));
            }
            if (file_exists($apiSource = $modulePath . 'Resource/api')) {
                Directory::copy($apiSource, Directory::get($compiledResourceDir . 'api'));
            }
            if (file_exists($umlSource = $modulePath . 'Resource/uml')) {
                Directory::copy($umlSource, Directory::get($compiledResourceDir . 'uml'));
            }
            if (file_exists($docSource = $modulePath . 'Resource/doc')) {
                Directory::copy($docSource, Directory::get($compiledResourceDir . 'doc'));
            }
        }
    }
}
