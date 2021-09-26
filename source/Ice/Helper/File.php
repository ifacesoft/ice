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
     * InfoSave data into file
     *
     * @param  $path
     * @param  $data
     * @param bool $phpData
     * @param int $file_put_contents_flag
     * @param bool $isPretty
     * @return mixed
     *
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function createData($path, $data, $phpData = true, $file_put_contents_flag = LOCK_EX, $isPretty = true)
    {
        if (!$path) {
            Core_Logger::getInstance(__CLASS__)->error('File path is empty', __FILE__, __LINE__);
            return $data;
        }

        $dir = Directory::get(dirname($path));

        $dataString = $phpData ? Php::varToPhpString($data, true, $isPretty) : $data;

        // todo: переписать на использование fopen + flock
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
     * @param bool $isRequire
     * @return mixed
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.13
     * @since   0.0
     */
    public static function loadData($path, $isRequire = true)
    {
        // todo: переписать на использование fopen + flock
        if ($path && file_exists($path) && filesize($path)) {
            try {
                return include $path;
            } catch (\Exception $e) {
                Core_Logger::getInstance(__CLASS__)->error('File include failed', __FILE__, __LINE__, $e);
            } catch (\Throwable $e) {
                Core_Logger::getInstance(__CLASS__)->error('File include failed', __FILE__, __LINE__, $e);
            }
        }

        if ($isRequire) {
            Core_Logger::getInstance(__CLASS__)->error(['File {$0} with data not found', $path], __FILE__, __LINE__);
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
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.2
     * @since   0.0
     */
    public static function move($from, $to, $isRequire = true)
    {
        if ($from === $to || empty($from) || empty($to)) {
            return false;
        }

        if (self::copy($from, $to, $isRequire)) {
            if (unlink($from)) {
                return true;
            } else {
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
     * @return bool
     * @throws Error
     * @throws Exception
     * @version 1.2
     * @since   0.2
     * @author dp <denis.a.shestakov@gmail.com>
     *
     */
    public static function copy($from, $to, $isRequire = true)
    {
        /** todo: использовать валидатор для урла */
        if (filter_var($from, FILTER_VALIDATE_URL)) {
            $headers = @get_headers($from);
            $isReadable = $headers && strpos($headers[0], '200') !== false;
        } else {
            $isReadable = is_file($from) && is_readable($from);
        }

        if (!$isReadable) {
            if ($isRequire) {
                throw  new Error(['Copy from {$0} to {$1} failed: Source file not readable', [$from, $to]]);
            } else {
                return false;
            }
        }

        $fromFile = fopen($from, 'rb');

        if (!$from || !$fromFile) {
            fclose($fromFile);

            if ($isRequire) {
                throw  new Error(['Copy from {$0} to {$1} failed: Can\'t open readable source file', [$from, $to]]);
            } else {
                return false;
            }
        }

        Directory::get(dirname($to));

        $toFile = fopen($to, 'wb');

        if (!$to || !$toFile) {
            fclose($toFile);

            if ($isRequire) {
                throw  new Error(['Copy from {$0} to {$1} failed: Can\'t open writable source file', [$from, $to]]);
            } else {
                return false;
            }
        }

        $length = 1024 * 8;

        while (!feof($fromFile)) {
            fwrite($toFile, fread($fromFile, $length), $length);
        }

        fclose($toFile);

        fclose($fromFile);

        return true;
    }

    /**
     * Return data from csv-file
     *
     * @param  $path
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param bool $isRequire
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

    public static function getContents($url, array $headers = [])
    {
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//            curl_setopt($ch, CURLOPT_HEADER, 1);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        // $output contains the output string
        $output = curl_exec($ch);

        if ($errno = curl_errno($ch)) {
            throw new Error('Curl (' . $url . ') error #' . $errno . ': ' .  curl_error($ch));
        }

        return $output;

        // close curl resource to free up system resources
        curl_close($ch);

        $arrContextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];

        return file_get_contents($url, false, stream_context_create($arrContextOptions));
    }
}
