<?php
/**
 * Ice core filter abstract class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;

/**
 * Class Filter
 *
 * Abstract validator class
 *
 * @see \Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class Filter extends Container
{
// todo: зачем stored
    use Stored;

    const DEFAULT_KEY = 'default';

    /**
     * Return filter instance
     *
     * @param  null $instanceKey
     * @param  null $ttl
     * @param array $params
     * @return Filter|Container
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.4
     * @since   1.4
     */
    public static function getInstance($instanceKey = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

    /**
     * Default action key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    protected static function getDefaultKey()
    {
        return Filter::DEFAULT_KEY;
    }

    /**
     * Default class key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.4
     * @since   1.4
     */
    protected static function getDefaultClassKey()
    {
        return self::getClass() . '/default';
    }

    /**
     * Filter data
     *
     * @param array $data
     * @param $name
     * @param  mixed $filterOptions
     * @return mixed
     * @author anonymous <email>
     *
     * @version 1.4
     * @since   1.4
     */
    abstract public function filter(array $data, $name, array $filterOptions);
}