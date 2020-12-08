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
use Ice\Core\Exception;
use Ice\Core\Logger as Core_Logger;
use Ice\Exception\Error;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Throwable;

/**
 * Class Directory
 *
 * Helper for directories
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Helper
 */
class Directory
{
    /**
     * Recursively copy directory
     *
     * @param $from
     *
     * @param $to
     * @param int $chmod
     * @return bool
     * @throws Error
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.11
     * @since   0.0
     * @throws Exception
     */
    public static function copy($from, $to, $chmod = 0777)
    {
        if (empty($from) || empty($to)) {
            throw new Error('Source or target path is empty');
        }

        $from = rtrim($from, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $to = rtrim($to, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        /** @var RecursiveDirectoryIterator $iterator */
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $from, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $path = $to . $iterator->getSubPathName();

            if ($item->isDir()) {
                self::get($path);
                continue;
            }

            if (!is_writable(dirname($path))) {
                Core_Logger::getInstance(__CLASS__)->warning(
                    [
                        'Copy directory {$0} failed. Permissions is wrong ({$1})',
                        [$path, substr(sprintf('%o', fileperms(dirname($path))), -4)]
                    ],
                    __FILE__,
                    __LINE__
                );

                return false;
            }

            copy($item, $path);

            if (function_exists('posix_getuid') && posix_getuid() == fileowner($path)) {
                chmod($path, $chmod);
//                    chgrp($path, filegroup(dirname($path)));
            }
        }

        return true;
    }

    /**
     * Recursively create directory
     *
     * @param  $path
     * @param int $chmod
     * @param bool $throwException
     * @return string
     * @throws Error
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.0
     * @since   0.0
     */
    public static function get($path, $chmod = 0777, $throwException = true)
    {
        if (is_dir($path)) {
            return realpath($path) . DIRECTORY_SEPARATOR;
        }

        $dir = self::get(dirname($path));

        if (!is_writable($dir)) {
            $error = ['Make directory {$0} failed. Permissions is wrong ({$1})', [$path, substr(sprintf('%o', fileperms($dir)), -4)]];

            if ($throwException) {
                Core_Logger::getInstance(__CLASS__)->exception($error,__FILE__, __LINE__);
            } else {
                Core_Logger::getInstance(__CLASS__)->warning($error, __FILE__, __LINE__);
            }

        }

        $old = umask(0);

        if (!file_exists($path)) {
            $error = ['Directory {$0} already exists or can not create', $path];

            try {
                if (!mkdir($path, $chmod) && !is_dir($path)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
                }
            } catch (\Exception $e) {
                Core_Logger::getInstance(__CLASS__)->warning($error, __FILE__, __LINE__);
            } catch (Throwable $e) {
                Core_Logger::getInstance(__CLASS__)->warning($error, __FILE__, __LINE__);
            }
        }

        umask($old);

//        if (function_exists('posix_getuid') && posix_getuid() == fileowner($path)) {
//            chgrp($path, filegroup($dir));
//        }

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
     * @return mixed
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
            $item->isDir() ? \rmdir($item->getPathname()) : \unlink($item->getPathname());
        }

        \rmdir($dirPath);

        return $dirPath;
    }

    public static function getFileNames($path)
    {
        return array_diff(scandir($path), ['..', '.']);
    }
}
