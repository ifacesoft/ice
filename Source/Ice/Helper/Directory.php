<?php
/**
 * Ice helper dir class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class Directory
 *
 * Helper for directories
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since 0.0
 */
class Directory
{
    /**
     * Recursively copy directory
     *
     * @param $source
     * @param $dest
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function copy($source, $dest)
    {
        foreach ($sourceDirectoryIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
            $path = self::get($dest) . $sourceDirectoryIterator->getSubPathName();

            if ($item->isDir()) {
                Directory::get($path);
            } else {
                copy($item, $path);

                if (function_exists('posix_getuid') && posix_getuid() == fileowner($path)) {
                    chmod($path, 0664);
                    chgrp($path, filegroup(dirname($path)));
                }
            }
        }
    }

    /**
     * Recursively create directory
     *
     * @param $path
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function get($path)
    {
        if (file_exists($path)) {
            return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        $dir = Directory::get(dirname($path));

        mkdir($path);

        if (function_exists('posix_getuid') && posix_getuid() == fileowner($path)) {
            chmod($path, 0775);
            chgrp($path, filegroup($dir));
        }

        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Recursively remove directory
     *
     * @param $dirPath
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function remove($dirPath)
    {
        if (!file_exists($dirPath)) {
            return;
        }

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($dirPath);
    }
} 