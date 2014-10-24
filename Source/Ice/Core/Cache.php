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

/**
 * Class Cache
 *
 * Core cache class
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version stable_0
 * @since stable_0
 */
class Cache
{
    use Core;

    /**
     * Validate cache
     *
     * @param $class
     * @param array $cacheTags
     * @param $time
     * @return bool
     */
    public static function validate($class, array $cacheTags, $time)
    {
        if (empty($cacheTags)) {
            return false;
        }

        /** @var Data_Provider $dataProvider */
        $dataProvider = Cache::getDataProvider('tags');

        $isValid = true;

        foreach (self::getKeys($cacheTags) as $key) {
            $tagCreate = $dataProvider->get($class . '/' . $key);

            if (!$tagCreate) {
                $tagCreate = time();
                $dataProvider->set($class . '/' . $key, $tagCreate);
            }

            if ($isValid) {
                $isValid = $tagCreate < $time;
            }
        }

        return $isValid;
    }

    /**
     * Return validateion keys
     *
     * @param $cacheTags
     * @return array
     */
    private static function getKeys($cacheTags)
    {
        $keys = [];

        foreach ($cacheTags as $tagKey => $tagValue) {
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
     * @param $class
     * @param $cacheTags
     */
    public static function invalidate($class, $cacheTags)
    {
        /** @var Data_Provider $dataProvider */
        $dataProvider = Cache::getDataProvider('tags');

        foreach (self::getKeys($cacheTags) as $key) {
            $dataProvider->set($class . '/' . $key, time());
        }
    }
} 