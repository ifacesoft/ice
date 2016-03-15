<?php
/**
 * Ice core cache class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\DataProvider\Repository;

/**
 * Class Cache
 *
 * Core cache class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Cache
{
    use Stored;

    const VALIDATE = 'validate';
    const INVALIDATE = 'invalidate';

    private $value = null;
    private $cacheable = null;

    /**
     * Private constructor for cache object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    private function __construct()
    {
    }

    /**
     * Create cache object
     *
     * @param  $cacheable
     * @param  $value
     * @return Cache
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.7
     * @since   0.5
     */
    public static function create($cacheable, $value)
    {
        $cache = new Cache();

        $cache->value = $value;
        $cache->cacheable = $cacheable;

        return $cache;
    }

    /**
     * Validate time tags
     *
     * @param  Cacheable $cacheable
     * @param  $cacheTime
     * @param  array $validateTags
     * @return Cacheable|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function validateTimeTags(Cacheable $cacheable, array $validateTags, $cacheTime)
    {
        if (empty($validateTags)) {
            return null;
        }

        $repository = Cache::getRepository($cacheable);

        foreach (Cache::getKeys($validateTags) as $key) {
            $time = $repository->get($key);

            if (!$time) {
                $time = microtime(true);
                $repository->set($key, $time);
            }

            if ($time >= $cacheTime) {
                return null;
            }
        }

        return $cacheable;
    }

    /**
     * Return cache repository
     *
     * @param  Cacheable $cacheable
     * @return Repository
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getRepository(Cacheable $cacheable)
    {
        return Repository::getInstance(__CLASS__, get_class($cacheable));
    }

    /**
     * Return validation keys
     *
     * @param  $tags
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function getKeys($tags)
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
     * @param  Cacheable $cacheable
     * @param  array $invalidateTags
     * @return Cacheable
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function invalidateTimeTags(Cacheable $cacheable, array $invalidateTags)
    {
        $repository = Cache::getRepository($cacheable);

        $time = microtime(true);

        foreach (self::getKeys($invalidateTags) as $key) {
            $repository->set($key, $time);
        }

        return $cacheable;
    }

    /**
     * Validate cache
     *
     * @return Cacheable|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.0
     */
    public function validate()
    {
        if ($this->cacheable instanceof Cacheable) {
            return $this->cacheable->validate($this->value);
        }

        return $this->cacheable;
    }
}
