<?php
/**
 * Ice core loader class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Exception\File_Not_Found;
use Ice\Helper\Object;

/**
 * Class Loader
 *
 * Register, unregister loaders and ice method load
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version stable_0
 * @since stable_0
 */
class Loader
{
    use Core;

    /** @var array Registrered autoloaders */
    private static $_autoLoaders = [];

    /**
     * Load class
     *
     * @param $class
     * @return bool
     * @throws Exception
     */
    public static function load($class)
    {
        if (class_exists($class, false)) {
            return;
        }

        /** @var Data_Provider $dataProvider */
        $dataProvider = Loader::getDataProvider();

        $fileName = $dataProvider->get($class);
        if ($fileName) {
            require_once $fileName;
            return;
        }

        $fileName = self::getFilePath($class, '.php', 'Source/');

        if (file_exists($fileName)) {
            require_once $fileName;

            if (class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false)) {
                $dataProvider->set($class, $fileName);
                return;
            }

            throw new Exception(['File {$0} exists, but class {$1} not found', [$fileName, $class]]);
        }

        throw new Exception('Class "' . $class . '" not found');
    }

    /**
     * Return class path
     *
     * @param $class
     * @param $ext
     * @param $path
     * @param bool $isRequired
     * @param bool $isNotEmpty
     * @param bool $isOnlyFirst
     * @param bool $allMatchedPathes
     * @throws File_Not_Found
     * @return null|string
     */
    public static function getFilePath(
        $class,
        $ext,
        $path,
        $isRequired = true,
        $isNotEmpty = false,
        $isOnlyFirst = false,
        $allMatchedPathes = false
    )
    {
        $fileName = null;

        $fullStackPathes = [];
        $matchedPathes = [];

        $modulePathes = Module::getPathes();

        if ($isOnlyFirst) {
            $modulePathes = [$modulePathes[Object::getModuleAlias($class)]];
        }

        foreach ($modulePathes as $modulePath) {
            $typePathes = [$path];

            $filePath = str_replace(['_', '\\'], '/', $class);

            foreach ($typePathes as $typePath) {
                $fileName = $modulePath . $typePath . $filePath . $ext;

                $fullStackPathes[] = $fileName;

//                Logger::debug($fileName . ' ' . (int)file_exists($fileName));
//                var_dump($fileName . ' ' . (int)file_exists($fileName));
                if (file_exists($fileName)) {
                    $matchedPathes[] = $fileName;

                    if (!$allMatchedPathes) {
                        return $fileName;
                    }
                }
            }
        }

        if ($isRequired) {
            if (!$allMatchedPathes || empty($matchedPathes)) {
                throw new File_Not_Found(['FileNotFoundException: {$0}', $class], $fullStackPathes);
            }
        }

        if ($allMatchedPathes) {
            return $matchedPathes;
        }

        return $isNotEmpty && !empty($fullStackPathes) ? reset($fullStackPathes) : '';
    }

    /**
     * Register class autoloader
     *
     * example:
     * ```php
     *      Loader::register('Ice\Core\Loader::load')
     * ```
     *
     * @param $autoLoader string autoload method
     */
    public static function register($autoLoader)
    {
        foreach (self::$_autoLoaders as $loader) {
            spl_autoload_unregister($loader);
        }

        $autoLoaders = self::$_autoLoaders;
        array_unshift($autoLoaders, $autoLoader);
        self::$_autoLoaders = $autoLoaders;

        foreach (self::$_autoLoaders as $loader) {
            spl_autoload_register($loader);
        }
    }

    /**
     * Unregister class autoloader
     *
     * example:
     * ```php
     *      Loader::unregister('Ice\Core\Loader::load')
     * ```
     *
     * @param $autholoader string string autoload method
     */
    public static function unregister($autholoader)
    {
        foreach (self::$_autoLoaders as $key => $loader) {
            if ($loader == $autholoader) {
                spl_autoload_unregister($autholoader);
                unset(self::$_autoLoaders[$key]);
                break;
            }
        }
    }
} 