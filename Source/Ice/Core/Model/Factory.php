<?php
/**
 * Ice core model defined factory abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

/**
 * Class Model_Factory
 *
 * Core factory abstract model class
 *
 * @see Ice\Core\Model
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since   0.0
 */
abstract class Model_Factory extends Model_Defined
{
    /**
     * Получение делегата модели
     *
     * @param  $delegateName
     * @param  string|null $sourceName
     * @param  int $ttl
     * @return Model|null
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getDelegate($delegateName, $sourceName = null, $ttl = null)
    {
        return Query::getBuilder(self::getClass())
            ->eq(['/delegate_name' => $delegateName])
            ->is('/active')
            ->executeSelect('/delegate_name')
            ->getModel($sourceName, $ttl);
    }
}
