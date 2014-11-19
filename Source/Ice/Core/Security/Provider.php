<?php
/**
 * Ice core security provider abstarct class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;

/**
 * Class Security_Provider
 *
 * Core security provider abstract class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.1
 * @since 0.1
 */
abstract class Security_Provider extends Container
{
    /**
     * Create new instance of security provider
     *
     * @param $key
     * @param null $hash
     * @return Security_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected static function create($key, $hash = null)
    {
        /** @var Security_Provider $class */
        $class = 'Ice\Security\Provider\\' . $key;

        return new $class();
    }

    /**
     * Return register form
     *
     * @param array $data
     * @return Form
     */
    abstract public function getRegisterForm(array $data = array());

    /**
     * Return login form
     *
     * @param array $data
     * @return Form
     */
    abstract public function getLoginForm(array $data = array());

    /**
     * Return instance of security provider
     *
     * @param null $key
     * @param null $ttl
     * @return Security_Provider
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }
}