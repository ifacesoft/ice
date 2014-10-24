<?php
/**
 * Ice helper file class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use Ice\Core\Logger as Core_Logger;

/**
 * Class File
 *
 * Helper for file operations
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version stable_0
 * @since stable_0
 */
class File
{
    /**
     * Save data into file
     *
     * @param $path
     * @param $data
     * @param bool $phpData
     * @param int $file_put_contents_flag
     * @return mixed
     */
    public static function createData($path, $data, $phpData = true, $file_put_contents_flag = 0)
    {
        if (empty($path)) {
            Core_Logger::getInstance()->error('File path is empty', __FILE__, __LINE__);
        }

        $dir = Directory::get(dirname($path));

        $dataString = $phpData ? Php::varToPhpString($data) : $data;
        file_put_contents($path, $dataString, $file_put_contents_flag);

        if (function_exists('posix_getuid') && posix_getuid() == fileowner($path)) {
            chmod($path, 0664);
            chgrp($path, filegroup($dir));
        }

        return $data;
    }

    /**
     * Load data from file
     *
     * @param $path
     * @return mixed
     */
    public static function loadData($path)
    {
        if (empty($path)) {
            Core_Logger::getInstance()->error('File path is empty', __FILE__, __LINE__);
        }

        return include $path;
    }

    /**
     * Rename/move file
     *
     * @param $from
     * @param $to
     */
    public static function move($from, $to)
    {
        Directory::get(dirname($to));
        rename($from, $to);
    }
} 