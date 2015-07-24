<?php
/**
 * Ice core loader class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Composer\Autoload\ClassLoader;
use Ice\Core;
use Ice\Data\Provider\Repository;
use Ice\Exception\FileNotFound;
use Ice\Helper\Hash;
use Ice\Helper\Object;

/**
 * Class Loader
 *
 * Register, unregister loaders and ice method load
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since   0.0
 */
class Loader
{
    use Core;

    /**
     * @var array Registred autoloaders
     */
    private static $autoLoaders = [];

    /**
     * Composer loader
     *
     * @var ClassLoader
     */
    private static $loader = null;

    private static $forceLoading = null;

    /**
     * @var Repository
     */
    private static $repository = null;

    /**
     * Load class
     *
     * @param  $class
     * @param  bool $isRequired
     * @return bool
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public static function load($class, $isRequired = true)
    {
        if (class_exists($class, false)) {
            return true;
        }

        if (self::$repository && $fileName = self::$repository->get($class)) {
            include_once $fileName;
            return true;
        }

        $fileName = self::getFilePath($class, '.php', Module::SOURCE_DIR, $isRequired);

        if (file_exists($fileName)) {
            include_once $fileName;

            if (class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false)) {
                self::$repository->set($class, $fileName);
                return true;
            }

            if (!self::$forceLoading && $isRequired) {
                Loader::getLogger()->exception(
                    ['File {$0} exists, but class {$1} not found', [$fileName, $class]],
                    __FILE__,
                    __LINE__
                );
            }
        }

        if (!self::$forceLoading && $isRequired) {
            Loader::getLogger()->exception(['Class {$0} not found', $class], __FILE__, __LINE__, null);
        }

        return false;
    }

    /**
     * Return class path
     *
     * @param  $class
     * @param  $ext
     * @param  $path
     * @param  bool $isRequired
     * @param  bool $isNotEmpty
     * @param  bool $isOnlyFirst
     * @param  bool $allMatchedPathes
     * @throws FileNotFound
     * @return null|string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
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

        if ($isOnlyFirst) {
            try {
                $modules = [Module::getInstance(Object::getModuleAlias($class))];
            } catch (\Exception $e) {
                $modules = [Module::getInstance()];
            }

        } else {
            $modules = Module::getAll();
        }

        foreach ($modules as $module) {
            $typePathes = $module->gets($path, false);

            if (empty($typePathes)) {
                $typePathes = [$path];
            }

            $filePath = str_replace(['_', '\\'], '/', $class);

            foreach ($typePathes as $typePath) {
                $fileName = $typePath . $filePath . $ext;

                $fullStackPathes[] = $fileName;

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
                if (self::$loader && $fileName = self::$loader->findFile($class)) {
                    if (!$allMatchedPathes) {
                        return $fileName;
                    }

                    $fullStackPathes[] = $fileName;
                    $matchedPathes[] = $fileName;
                } else {
                    if (self::$forceLoading) {
                        return null;
                    } else {
                        Loader::getLogger()->exception(
                            ['Files for {$0} not found', $class],
                            __FILE__,
                            __LINE__,
                            null,
                            $fullStackPathes,
                            -1,
                            'Ice:FileNotFound'
                        );
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
     * @since   0.0
     */
    public static function unregister($autholoader)
    {
        foreach (self::$autoLoaders as $key => $loader) {
            if ($loader == $autholoader) {
                spl_autoload_unregister($autholoader);
                unset(self::$autoLoaders[$key]);
                break;
            }
        }
    }

    public static function init(ClassLoader $loader, $forceLoading = false)
    {
        self::$loader = $loader;
        self::$forceLoading = $forceLoading;

        self::$repository = Loader::getRepository();

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
     * @since   0.0
     */
    public static function register($autoLoader)
    {
        foreach (self::$autoLoaders as $loader) {
            spl_autoload_unregister($loader);
        }

        array_unshift(self::$autoLoaders, $autoLoader);

        foreach (self::$autoLoaders as $loader) {
            spl_autoload_register($loader);
        }
    }
}
