<?php
/**
 * Ice core cachable interface
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */
namespace Ice\Core;

/**
 * Interface Cacheble
 *
 * Core cachable interface
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version stable_0
 * @since stable_0
 */
interface Cacheable
{
    /**
     * Return data from cache
     *
     * @param $data
     * @param $hash
     * @return mixed
     */
    public static function getCache($data, $hash);
}