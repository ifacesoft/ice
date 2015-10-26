<?php
/**
 * Ice helper dir class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Helper;

use FilesystemIterator;
use Ice\Core\Logger as Core_Logger;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class Directory
 *
 * Helper for directories
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 *
 * @version 0.0
 * @since   0.0
 */
class Directory
{
    /**
     * Recursively copy directory
     *
     * @param $source
     * @param $dest
     *
     * @param int $chmod
     * @throws \Ice\Core\Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   0.0
     */
    public static function copy($source, $dest, $chmod = 0777)
    {
        /** @var RecursiveDirectoryIterator $iterator */
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $path = self::get($dest) . $iterator->getSubPathName();

            if ($item->isDir()) {
                Directory::get($path);
            } else {
                if (!is_writable(dirname($path))) {
                    Core_Logger::getInstance(__CLASS__)->exception(
                        [
                            'Copy directory {$0} failed. Permissions is wrong ({$1})',
                            [$path, substr(sprintf('%o', fileperms(dirname($path))), -4)]
                        ],
                        __FILE__,
                        __LINE__
                    );
                }

                copy($item, $path);

                if (function_exists('posix_getuid') && posix_getuid() == fileowner($path)) {
                    chmod($path, $chmod);
//                    chgrp($path, filegroup(dirname($path)));
                }
            }
        }
    }

    /**
     * Recursively create directory
     *
     * @param  $path
     * @param int $chmod
     * @return string
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   0.0
     */
    public static function get($path, $chmod = 0777)
    {
        if (file_exists($path)) {
            return realpath($path) . DIRECTORY_SEPARATOR;
        }

        $dir = Directory::get(dirname($path));

        if (!is_writable($dir)) {
            Core_Logger::getInstance(__CLASS__)->exception(
                [
                    'Make directory {$0} failed. Permissions is wrong ({$1})',
                    [$path, substr(sprintf('%o', fileperms($dir)), -4)]
                ],
                __FILE__,
                __LINE__
            );
        }

        $old = umask(0);
        mkdir($path, $chmod);
        umask($old);

        if (function_exists('posix_getuid') && posix_getuid() == fileowner($path)) {
//            chgrp($path, filegroup($dir));
        }

        return realpath($path) . DIRECTORY_SEPARATOR;
    }

    /**
     * Recursively remove directory
     *
     * @param $dirPath
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public static function remove($dirPath)
    {
        if (!file_exists($dirPath)) {
            return $dirPath;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($dirPath);

        return $dirPath;
    }

    public static function getFileNames($path)
    {
        return array_diff(scandir($path), ['..', '.']);
    }
}
