<?php
/**
 * Ice core cache class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Data\Provider\Repository;

/**
 * Class Cache
 *
 * Core cache class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 */
class Cache
{
    const VALIDATE = 'validate';
    const INVALIDATE = 'invalidate';

    private $_tags = null;
    private $_time = null;
    private $_object = null;

    private function __construct($object, $time)
    {
        $this->_tags = $object->getCacheTags;
        $this->_time = $time;
        $this->_object = $object;
    }

    public static function create($object, $time) {
        return new Cache($object, $time);
    }

    /**
     * Validate cache
     *
     * @return Cacheable
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public function validate()
    {
        $repository = Repository::getInstance(__CLASS__, get_class($this->_object));

        foreach ($this->getKeys($this->_tags[Cache::VALIDATE]) as $key) {
            $time = $repository->get($key);

            if (!$time) {
                $time = time();
                $repository->set($key, $time);
            }

            if ($time >= $this->_time) {
                return null;
            }
        }

        return $this->_object;
    }

    /**
     * Return validation keys
     *
     * @param $tags
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function getKeys($tags)
    {
        $keys = [];

        foreach ($tags as $tagKey => $tagValue) {
            if (is_array($tagValue)) {
                $newKeys = self::getKeys($tagValue);

                foreach ($newKeys as &$tag) {
                    $tag = $tagKey . '/' . $tag;
                }

                $keys = array_merge($keys, $newKeys);
            } else {
                $keys[] = $tagKey;
            }
        }

        return $keys;
    }

    /**
     * Invalidation cache
     *
     * @param $time
     * @return Cacheable
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     *
     */
    public function invalidate($time)
    {
        $repository = Repository::getInstance(__CLASS__, get_class($this->_object));

        foreach (self::getKeys($this->_tags[Cache::VALIDATE]) as $key) {
            $repository->set($key, $time);
        }

        return $this->_object;
    }
} 