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
use Ice\Core\Debuger;
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
class QueryResultRow extends Registry
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

        $queryBuilderClass = $this->getKey();

        $connection->offsetSet(
            $this->getKeyPrefix(),
            $queryBuilderClass::create(null)->getSelectQuery('*')->getRow()
        );

        return true;
    }
}
