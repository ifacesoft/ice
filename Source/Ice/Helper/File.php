<?php
/**
 * Ice helper file class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;
use Ice\Exception\Error;

/**
 * Class File
 *
 * Helper for file operations
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 *
 * @version 0.2
 * @since   0.0
 */
class File
{
    /**
     * Save data into file
     *
     * @param  $path
     * @param  $data
     * @param  bool $phpData
     * @param  int $file_put_contents_flag
     * @param bool $isPretty
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function createData($path, $data, $phpData = true, $file_put_contents_flag = 0, $isPretty = true)
    {
        if (!$path) {
            Core_Logger::getInstance(__CLASS__)->error('File path is empty', __FILE__, __LINE__);
            return $data;
        }

        $dir = Directory::get(dirname($path));

        $dataString = $phpData ? Php::varToPhpString($data, true, $isPretty) : $data;
        file_put_contents($path, $dataString, $file_put_contents_flag);

        if (function_exists('posix_getuid') && posix_getuid() == fileowner($path)) {
            chmod($path, 0666);
//            chgrp($path, filegroup($dir));
        }

        return $data;
    }

    /**
     * Load data from file
     *
     * @param  $path
     * @param  bool $isRequire
     * @return mixed
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.0
     */
    public static function loadData($path, $isRequire = true)
    {
        if (empty($path)) {
            Core_Logger::getInstance(__CLASS__)->error('File path is empty', __FILE__, __LINE__);
        }

        if (file_exists($path)) {
            return include $path;
        }

        if ($isRequire) {
            Core_Logger::getInstance(__CLASS__)->exception(['File {$0} with data not found', $path], __FILE__, __LINE__);
        }

        return null;
    }

    /**
     * Rename/move file
     *
     * @param $from
     * @param $to
     * @param bool $isRequire
     * @return mixed
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public static function move($from, $to, $isRequire = true)
    {
        if (File::copy($from, $to, $isRequire)) {
            if (unlink($from)) {
                if ($isRequire) {
                    throw  new Error(['Remove file {$0} failed', $from]);
                } else {
                    return false;
                }
            }
        }

        if ($isRequire) {
            throw  new Error(['Move from {$0} to {$1} failed', [$from, $to]]);
        } else {
            return false;
        }
    }

    /**
     * Copy file
     *
     * @param $from
     * @param $to
     *
     * @param bool $isRequire
     * @return mixed
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.2
     */
    public static function copy($from, $to, $isRequire = true)
    {
        if (!file_exists($from)) {
            if ($isRequire) {
                throw  new Error(['Source file {$0} not found', $from]);
            } else {
                return false;
            }
        }

        Directory::get(dirname($to));

        if (copy($from, $to) === true) {
            return $to;
        }

        if ($isRequire) {
            throw  new Error(['Copy from {$0} to {$1} failed', [$from, $to]]);
        } else {
            return false;
        }
    }

    /**
     * Return data from csv-file
     *
     * @param  $path
     * @param  string $delimiter
     * @param  string $enclosure
     * @param  string $escape
     * @param  bool $isRequire
     * @return array|null
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public static function loadCsvData($path, $delimiter = ',', $enclosure = '"', $escape = '\\', $isRequire = true)
    {
        if (empty($path)) {
            Core_Logger::getInstance(__CLASS__)->error('File path is empty', __FILE__, __LINE__);
        }

        //        if (file_exists($path)) {
        //            return array_map(
        //               function ($row) use ($delimiter, $enclosure, $escape) {
        //                   return str_getcsv($row, $delimiter, $enclosure, $escape);
        //               },
        //               file($path)
        //           );
        //        }

        if ($handle = fopen($path, "r")) {
            $csvData = [];

            while ($data = fgetcsv($handle, 4096, $delimiter, $enclosure, $escape)) {
                $csvData[] = $data;
            }

            fclose($handle);

            return $csvData;
        }

        if ($isRequire) {
            Core_Logger::getInstance(__CLASS__)->exception(['File {$0} with data not found', $path], __FILE__, __LINE__);
        }

        return null;
    }
}
