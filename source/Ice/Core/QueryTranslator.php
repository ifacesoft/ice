<?php
/**
 * Ice core query translator class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Exception\DataSource as Exception_DataSource;

/**
 * Class QueryTranslator
 *
 * Core query translator abstract class
 *
 * @see \Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
abstract class QueryTranslator extends Container
{
    use Stored;

    /**
     * Return instance of query translator
     *
     * @param  null $instanceKey
     * @param  null $ttl
     * @param array $params
     * @return Core|Container
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     * @throws Exception
     */
    public static function getInstance($instanceKey = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($instanceKey, $ttl, $params);
    }

    /**
     * Translate query body
     *
     * @param Query $query
     * @param DataSource $dataSource
     * @return string|array
     * @throws Exception_DataSource
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function translate(Query $query, DataSource $dataSource)
    {
        $body = null;

        $queryBuilder = $query->getQueryBuilder();
        
        foreach (array_keys($queryBuilder->getSqlParts()) as $sqlPart) {
            $translate = 'translate' . ucfirst($sqlPart);

            $result = $this->$translate($query, $dataSource);

            if ($body === null) {
                $body = is_string($result) ? '' : [];
            }

            if (is_string($result)) {
                $body .= $result;
            } else {
                $body += $result;
            }
        }

        if (empty($body)) {
            throw new Exception_DataSource('Query is empty', $queryBuilder->getSqlParts());
        }

        return $body . "\n";
    }
}
