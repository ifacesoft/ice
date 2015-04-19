<?php
/**
 * Ice action layout main class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Action;

use Ice\Core\Action;

/**
 * Class Layout_Main
 *
 * Default ice action layout
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
class Layout_Main extends Layout
{
    protected static function config()
    {
        return array_merge_recursive(
            [
                'actions' => 'Ice:Resources',
            ],
            parent::config()
        );
    }
}
