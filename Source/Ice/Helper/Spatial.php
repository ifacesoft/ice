<?php
/**
 * Ice helper spatial class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Exception;
use Ice\Core\Logger;

/**
 * Class Spatial
 *
 * Helper for spatial functions
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since   0.0
 */
class Spatial
{
    /**
     * Decode spatial data
     *
     * String in geo data format to array of latitude and longitude
     *
     * @param  $geoData
     * @return array
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function decode($geoData)
    {
        $type = strstr($geoData, '(', true);

        switch ($type) {
            case 'POLYGON':
                preg_match_all('|\(\(([^)]+?)\)\)|', $geoData, $matches);
                $coordinates = [];

                foreach (explode(',', $matches[1][0]) as $value) {
                    list($longitude, $latitude) = explode(' ', $value);

                    $coordinates[] = [
                        'latitude' => $latitude,
                        'longitude' => $longitude
                    ];
                }

                return [
                    'type' => $type,
                    'coordinates' => $coordinates
                ];
            case 'POINT':
                preg_match_all('|\(([^)]+?)\)|', $geoData, $matches);
                list($longitude, $latitude) = explode(' ', $matches[1][0]);

                return [
                    'type' => $type,
                    'coordinates' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude
                    ]
                ];
            default:
                Logger::getInstance(__CLASS__)->exception(['Unknown spatial type {$0}', $type], __FILE__, __LINE__);
        }

        return $geoData;
    }

    /**
     * Encode array spatial data
     *
     * Array to string in geo data format
     *
     * @param  $geoData
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function encode($geoData)
    {
        var_dump($geoData);
        return $geoData;
    }
}
