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

/**
 * Class QueryTranslator
 *
 * Core query translator abstract class
 *
 * @see Ice\Core\Container
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
     * @param  null $key
     * @param  null $ttl
     * @param array $params
     * @return QueryTranslator
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public static function getInstance($key = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
    }

    /**
     * Translate query body
     *
     * @param  array $sqlParts
     * @return string|array
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public function translate(array $sqlParts)
    {
        $body = null;

        foreach ($sqlParts as $sqlPart => $part) {
            if (empty($part)) {
                continue;
            }

            $translate = 'translate' . ucfirst($sqlPart);

            $result = $this->$translate($part);

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
            Logger::getInstance(__CLASS__)->exception('Query body is empty', __FILE__, __LINE__, null, $sqlParts);
        }

        return $body;
    }
}
