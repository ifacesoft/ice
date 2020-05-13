<?php
/**
 * Ice data provider implementation request class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataProvider;

use ArrayObject;
use Ice\Core\DataProvider;
use Ice\Core\Exception;
use Ice\Core\Request as Core_Request;
use Ice\Exception\Error;

/**
 * Class Request
 *
 * Data provider for request data
 *
 * @see \Ice\Core\DataProvider
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataProvider
 */
class Request extends Registry
{
    const DEFAULT_KEY = 'default';

    /**
     * Set data to data provider
     *
     * @param array $values
     * @param  null $ttl
     * @return array
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    public function set(array $values = null, $ttl = null)
    {
        throw new Error('Request data provider is not unchangeable');
    }

    public function getIndex()
    {
        return Core_Request::class;
    }

    public function getKey()
    {
        return Request::getDefaultKey();
    }

    /**
     * Return default data provider key
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected static function getDefaultKey()
    {
        return self::DEFAULT_KEY;
    }

    /**
     * Delete from data provider by key
     *
     * @param  string $key
     * @param  bool $force if true return boolean else deleted value
     * @throws Exception
     * @return mixed|boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo 1.3 implement multiply (array keys) delete for other providers
     *
     * @version 1.3
     * @since   0.0
     */
    public function delete($key, $force = true)
    {
        throw new Error('Request data provider is not unchangeable');
    }

    /**
     * Connect to data provider
     *
     * @param  ArrayObject $connection
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.3
     * @since   0.0
     */
    protected function connect(&$connection)
    {
        parent::connect($connection);

        $connection->offsetSet(
            $this->getKeyPrefix(),
            array_merge(
                Core_Request::getParam(),
                [
                    'agent' => Core_Request::agent(),
                    'ip' => Core_Request::ip(),
                    'host' => Core_Request::host(),
                    'method' => Core_Request::method(),
                    'locale' => Core_Request::locale(),
                    'query_string' => Core_Request::queryString(),
                    'referer' => Core_Request::referer(),
                    'uri' => Core_Request::uri(),
                    'protocol' => Core_Request::protocol()
                ]
            )
        );

        return true;
    }

    /**
     * Check for errors
     *
     * @return void
     *
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    function checkErrors()
    {
        // TODO: Implement checkErrors() method.
    }
}
