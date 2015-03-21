<?php
/**
 * Ice core module class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Directory;
use Ice\Helper\File;
use Ice\Helper\String;

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
 */
class Module extends Config
{
    const CONFIG_DIR = 'configDir';
    const SOURCE_DIR = 'sourceDir';
    const RESOURCE_DIR = 'resourceDir';
    const LOG_DIR = 'logDir';
    const CACHE_DIR = 'cacheDir';
    const UPLOAD_DIR = 'uploadDir';
    const DOWNLOAD_DIR = 'downloadDir';
    const COMPILED_RESOURCE_DIR = 'compiledResourceDir';

    /**
     * All available modules
     *
     * @var array
     */
    private static $_modules = null;

    /**
     * Get module instance by module alias
     *
     * @param string $moduleAlias
     * @param null $postfix
     * @param bool $isRequired
     * @param null $ttl
     * @return Module
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public static function getInstance($moduleAlias = null, $postfix = null, $isRequired = false, $ttl = null)
    {
        $modules = Module::getAll();

        if (!$moduleAlias) {
            return reset($modules);
        }

        if (!isset($modules[$moduleAlias])) {
            Module::getLogger()->exception(['Module alias {$0} not found in module config files', $moduleAlias], __FILE__, __LINE__);
        }

        return $modules[$moduleAlias];
    }

    /**
     * Return array of Modules
     *
     * @return Module[]
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    public static function getAll()
    {
        if (self::$_modules === null) {
            self::$_modules = [];

            Module::loadConfig('', '', self::$_modules, MODULE_CONFIG_PATH);
        }

        return self::$_modules;
    }

    /**
     * Load module configs
     *
     * @param $vendor
     * @param string $context
     * @param array $modules
     *
     * @param string $configFilePath
     * @throws \ErrorException
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.0
     */
    private static function loadConfig($vendor, $context, array &$modules = [], $configFilePath = 'Config/Ice/Core/Module.php')
    {
        $modulePath = $vendor
            ? VENDOR_DIR . $vendor . '/'
            : MODULE_DIR;

        $configPath = $modulePath . $configFilePath;

        $moduleConfig = File::loadData($configPath);

        if (!$moduleConfig) {
            throw new \ErrorException('Module loading failed. File ' . $configPath . ' not found');
        }

        $module = $moduleConfig['module'];

        $module['path'] = $modulePath;

        $dirs = [
            MODULE::CONFIG_DIR,
            MODULE::SOURCE_DIR,
            MODULE::RESOURCE_DIR,
            MODULE::LOG_DIR,
            MODULE::CACHE_DIR,
            MODULE::UPLOAD_DIR,
            MODULE::DOWNLOAD_DIR,
            MODULE::COMPILED_RESOURCE_DIR
        ];

        foreach ($dirs as $dir) {
            $module[$dir] = Directory::get($modulePath . $module[$dir]);
        }

        $module['context'] = $context;

        if (isset($modules[$moduleConfig['alias']])) {
            unset($modules[$moduleConfig['alias']]);
            $modules[$moduleConfig['alias']] = Module::create($moduleConfig['alias'], $module);
            return;
        }

        $modules[$moduleConfig['alias']] = Module::create($moduleConfig['alias'], $module);

        foreach ($moduleConfig['vendors'] as $vendor => $context) {
            Module::loadConfig($vendor, $context, $modules);
        }
    }

    /**
     * Return table prefixes for module
     *
     * @param $dataSourceKey
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.5
     */
    public function getDataSourcePrefixes($dataSourceKey)
    {
        $dataSources = $this->getDataSources();
        return (array)$dataSources[$dataSourceKey];
    }

    /**
     * Check table belongs to module
     *
     * @param $tableName
     * @param $dataSourceKey
     * @return bool
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function checkTableByPrefix($tableName, $dataSourceKey)
    {
        return String::startsWith($tableName, $this->getDataSourcePrefixes($dataSourceKey));
    }

    public function getModelClass($tableName, $dataSourceKey)
    {
        $alias = null;
        $tableNamePart = $tableName;

        foreach ($this->getDataSourcePrefixes($dataSourceKey) as $prefix) {
            if (String::startsWith($tableName, $prefix)) {
                $alias = $this->getAlias();
                $tableNamePart = substr($tableName, strlen($prefix));
                break;
            }
        }

        if (!$alias) {
            $alias = Module::getInstance()->getAlias();
        }

        $modelName = $alias . '\Model\\';

        foreach (explode('_', preg_replace('/_{2,}/', '_', $tableNamePart)) as $modelNamePart) {
            $modelName .= ucfirst($modelNamePart) . '_';
        }

        return rtrim($modelName, '_');
    }

    public function getDataSources()
    {
        return $this->gets(Data_Source::getClass());
    }

    public function getDataSourceTables()
    {
        $tables = [];

        foreach ($this->getDataSourceKeys() as $dataSourceKey) {
            $tables[$dataSourceKey] = Data_Source::getInstance($dataSourceKey)->getTables($this);
        }

        return $tables;
    }

    public function getDataSourceKeys()
    {
        return array_keys($this->getDataSources());
    }

    public function getAlias()
    {
        return $this->getConfigName();
    }
}