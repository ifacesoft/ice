<?php
/**
 * Ice core loader class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Composer\Autoload\ClassLoader;
use Ice\Core;
use Ice\Data\Provider\Repository;
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
 * @version 0.0
 * @since 0.0
 */
class Loader
{
    use Core;

    /** @var array Registred autoloaders */
    private static $_autoLoaders = [];

    /**
     * Composer loader
     *
     * @var ClassLoader
     */
    private static $_loader = null;

    private static $_forceLoading = null;

    /**
     * @var Repository
     */
    private static $_repository = null;
    /**
     * Load class
     *
     * @param $class
     * @param bool $isRequired
     * @return bool
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public static function load($class, $isRequired = true)
    {
        if (class_exists($class, false)) {
            return true;
        }

        $fileName = self::$_repository->get($class);
        if ($fileName) {
            require_once $fileName;
            return true;
        }

        $fileName = self::getFilePath($class, '.php', Module::SOURCE_DIR, $isRequired);

        if (file_exists($fileName)) {
            require_once $fileName;

            if (class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false)) {
                self::$_repository->set($class, $fileName);
                return true;
            }

            if (!self::$_forceLoading && $isRequired) {
                Loader::getLogger()->exception(['File {$0} exists, but class {$1} not found', [$fileName, $class]], __FILE__, __LINE__);
            }
        }

        if (!self::$_forceLoading && $isRequired) {
            Loader::getLogger()->exception(['Class {$0} not found', $class], __FILE__, __LINE__, null);
        }

        return false;
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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

            $modules = $isOnlyFirst
            ? [Module::getInstance(Object::getModuleAlias($class))]
            : Module::getAll();

        foreach ($modules as $module) {
            $typePathes = $module->gets($path, false);

            if (empty($typePathes)) {
                $typePathes = [$path];
            }

            $filePath = str_replace(['_', '\\'], '/', $class);

            foreach ($typePathes as $typePath) {
                $fileName = $typePath . $filePath . $ext;

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
                if (self::$_loader && $fileName = self::$_loader->findFile($class)) {
                    if (!$allMatchedPathes) {
                        return $fileName;
                    }

                    $fullStackPathes[] = $fileName;
                    $matchedPathes[] = $fileName;
                } else {
                    if (self::$_forceLoading) {
                        return null;
                    } else {
                        Loader::getLogger()->exception(['Files for {$0} not found', $class], __FILE__, __LINE__, null, $fullStackPathes, -1, 'Ice:File_Not_Found');
                    }
                }
            }
        }

        if ($allMatchedPathes) {
            return $matchedPathes;
        }

        return $isNotEmpty && !empty($fullStackPathes) ? reset($fullStackPathes) : '';
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
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
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

    public static function init(ClassLoader $loader, $forceLoading = false)
    {
        self::$_loader = $loader;
        self::$_forceLoading = $forceLoading;

        self::$_repository = Loader::getRepository();

        spl_autoload_unregister([$loader, 'loadClass']);

        Loader::register([$loader, 'loadClass']);
        Loader::register('Ice\Core\Loader::load');
    }

    /**
     * Register class autoloader
     *
     * example:
     * ```php
     *      Loader::register('Ice\Core\Loader::load')
     * ```
     *
     * @param $autoLoader array autoloaders method
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public static function register($autoLoader)
    {
        foreach (self::$_autoLoaders as $loader) {
            spl_autoload_unregister($loader);
        }

        array_unshift(self::$_autoLoaders, $autoLoader);

        foreach (self::$_autoLoaders as $loader) {
            spl_autoload_register($loader);
        }
    }
}