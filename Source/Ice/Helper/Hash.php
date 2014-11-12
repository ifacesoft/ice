<?php
/**
 * Ice helper hash class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;


use Ice\Core\Exception;

/**
 * Class Hash
 *
 * Helper for hashes
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since 0.0
 */
class Hash
{
    const HASH_MD5 = 'md5';
    const HASH_CRC32 = 'crc32';
    const HASH_CRC32B = 'crc32b';
    const HASH_SHA1 = 'sha1';

    /**
     * Get hash by serializer and hash type
     *
     * @param mixed $data
     * @param string $serializer
     * @param string $hash
     * @throws \Ice\Core\Exception
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function get($data, $hash = null, $serializer = null)
    {
        $serializer = Serializer::getSerializer($serializer);

        if ($serializer == Serializer::SERIALIZER_IGBINARY && $hash == self::HASH_CRC32) {
            $hash = self::HASH_CRC32B;
        }

        if ($hash === null) {
            $hash = self::HASH_MD5;
        }

        $data = Serializer::serialize($data, $serializer);

        switch ($hash) {
            case self::HASH_MD5:
                return md5($data);
            case self::HASH_CRC32:
                return crc32($data);
            case self::HASH_CRC32B:
                return hash(self::HASH_CRC32B, $data);
            case self::HASH_SHA1:
                return sha1($data);
            default:
                throw new Exception('Unknown hash "' . $hash . "");
        }
    }
} 