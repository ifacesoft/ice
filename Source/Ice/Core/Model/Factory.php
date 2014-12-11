<?php
/**
 * Ice core model defined factory abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core\Model;

/**
 * Class Model_Factory
 *
 * Core factory abstract model class
 *
 * @see Ice\Core\Model
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
abstract class Model_Factory extends Model_Defined
{
    /**
     * Получение делегата модели
     *
     * @param $delegateName
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getDelegate($delegateName, $sourceName = null, $ttl = 3600)
    {
        /** @var Model $modelclass */
        $modelclass = get_called_class();

        return $modelclass::getQueryBuilder()
            ->select('/delegate_name')
            ->eq('/delegate_name', $delegateName)
            ->eq('/is_active', 1)
            ->getModel($sourceName, $ttl);
    }
}