<?php
/**
 * Ice core module class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Helper\File;

/**
 * Class Module
 *
 * Core module class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
class Module extends Container
{
    use Core;

    /**
     * All available modules
     *
     * @var array
     */
    private static $_modules = null;

    /**
     * All module aliases
     *
     * @var array
     */
    private static $_aliases = null;

    /**
     * All module pathes
     *
     * @var array
     */
    private static $_pathes = null;

    /**
     * Main module alias
     *
     * @var string
     */
    private $_moduleAlias = null;

    /**
     * Main module
     *
     * @var Module
     */
    private $_module = null;

    /**
     * Private constructor of module
     *
     * @param $moduleAlias
     * @param $module
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct($moduleAlias, $module)
    {
        $this->_moduleAlias = $moduleAlias;
        $this->_module = $module;
    }

    /**
     * Create new instance of module
     *
     * @param $moduleAlias
     * @param $hash
     * @return Module
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($moduleAlias, $hash = null)
    {
        return new Module($moduleAlias, self::get($moduleAlias));
    }

    /**
     * Get module by module alias
     *
     * @param string $moduleAlias
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function get($moduleAlias = null)
    {
        if (self::$_modules === null) {
            self::$_modules = [];
            Module::loadConfig(MODULE_DIR, '', self::$_modules);

            $iceModuleConfig = File::loadData(ICE_DIR . 'Config/Ice/Core/Module.php')['module'];
            $iceModuleConfig['path'] = ICE_DIR;
            $iceModuleConfig['context'] = '/ice';

            self::$_modules['Ice'] = $iceModuleConfig;
        }

        return empty($moduleAlias) ? self::$_modules : self::$_modules[$moduleAlias];
    }

    /**
     * Load module configs
     *
     * @param $moduleDir
     * @param $context
     * @param array $modules
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    private static function loadConfig($moduleDir, $context = '/', array &$modules = [])
    {
        $configPath = $moduleDir . 'Config/Ice/Core/Module.php';

        $moduleConfig = File::loadData($configPath);

        if (!$moduleConfig) {
            return;
        }

        $moduleConfig['module']['path'] = $moduleDir;
        $moduleConfig['module']['context'] = $context;

        if (isset($modules[$moduleConfig['alias']])) {
            unset($modules[$moduleConfig['alias']]);
            $modules[$moduleConfig['alias']] = $moduleConfig['module'];
            return;
        }

        $modules[$moduleConfig['alias']] = $moduleConfig['module'];

        foreach ($moduleConfig['modules'] as $moduleDir => $context) {
            Module::loadConfig($moduleDir, $context, $modules);
        }
    }

    /**
     * Return module aliases
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getAliases()
    {
        return self::$_aliases === null
            ? self::$_aliases = array_keys(self::get())
            : self::$_aliases;
    }

    /**
     * Return module pathes
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getPathes()
    {
        return self::$_pathes === null
            ? self::$_pathes = array_map(
                function ($module) {
                    return is_array($module['path']) ? reset($module['path']) : $module['path'];
                }, Module::get())
            : self::$_pathes;
    }

    /**
     * Return main module path
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getPath()
    {
        return is_array($this->_module['path']) ? reset($this->_module['path']) : $this->_module['path'];
    }

    /**
     * Return main module path
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getAlias()
    {
        return $this->_moduleAlias;
    }

    /**
     * Return default module alias key
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        $aliases = self::getAliases();
        return reset($aliases);
    }

    /**
     * Return instance of module
     *
     * @param null $key
     * @param null $ttl
     * @return Module
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }
}