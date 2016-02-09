<?php
/**
 * Ice core module class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Exception\ModuleNotFound;
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
 * @package    Ice
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
    const DATA_DIR = 'dataDir';
    const TEMP_DIR = 'tempDir';
    const DOWNLOAD_DIR = 'downloadDir';
    const COMPILED_RESOURCE_DIR = 'compiledResourceDir';

    public static $defaultConfig = [
        'alias' => 'Draft',
        'module' => [
            Module::CONFIG_DIR => 'Config/',
            Module::SOURCE_DIR => 'Source/',
            Module::RESOURCE_DIR => 'Resource/',
            Module::LOG_DIR => 'Var/log/',
            Module::CACHE_DIR => 'Var/cache/',
            Module::UPLOAD_DIR => 'Var/upload/',
            Module::DATA_DIR => 'Var/temp/',
            Module::TEMP_DIR => 'Var/temp/',
            Module::COMPILED_RESOURCE_DIR => 'Web/resource/',
            Module::DOWNLOAD_DIR => 'Web/download/',
            'ignorePatterns' => [],
            'bootstrapClass' => 'Ice\Bootstrap\Ice',

        ],
        'modules' => [
            'ifacesoft/ice' => '/ice'
        ]
    ];

    /**
     * All available modules
     *
     * @var array
     */
    private static $modules = null;

    private static $defaultDataSourceKeys = null;

    /**
     * Check table belongs to module
     *
     * @param  $tableName
     * @param  $dataSourceKey
     * @return bool
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.7
     * @since   0.5
     */
    public function checkTableByPrefix($tableName, $dataSourceKey)
    {
        foreach ($this->getDataSourcePrefixes($dataSourceKey) as $prefixes) {
            if (String::startsWith($tableName, $prefixes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return table prefixes for module
     *
     * @param  $dataSourceKey
     * @return array
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.7
     * @since   0.5
     */
    public function getDataSourcePrefixes($dataSourceKey)
    {
        $prefixes = [];

        foreach (Module::getAll() as $module) {
            $isProject = $module->getName() == $this->getName();

            foreach ($module->getDataSources() as $key => $tablePrefixes) {
                $dataSourceName = strstr($key, '/', true);

                if (
                    ($isProject && $dataSourceKey == $key) ||
                    (!$isProject && $dataSourceName == strstr($dataSourceKey, '/', true))
                ) {
                    $alias = $module->getAlias();

                    if (!isset($prefixes[$alias])) {
                        $prefixes[$alias] = [];
                    }

                    $prefixes[$alias] += (array)$tablePrefixes;
                }
            }
        }


        return $prefixes;
    }

    public function getDataSources()
    {
        return $this->gets(DataSource::getClass());
    }

    public function getModelClass($tableName, $dataSourceKey)
    {
        $alias = null;
        $tableNamePart = $tableName;

        foreach ($this->getDataSourcePrefixes($dataSourceKey) as $moduleAlias => $prefixes) {
            foreach ($prefixes as $prefix) {
                if (String::startsWith($tableName, $prefix)) {
                    $tableNamePart = substr($tableName, strlen($prefix));
                    $alias = $moduleAlias;
                    break 2;
                }
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

    public function getAlias()
    {
        return $this->getName();
    }

    /**
     * Get module instance by module alias
     *
     * @param  string $moduleAlias
     * @param  null $postfix
     * @param  bool $isRequired
     * @param  null $ttl
     * @return Module
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    public static function getInstance($moduleAlias = null, $postfix = null, $isRequired = false, $ttl = null)
    {
        $modules = Module::getAll();

        if (!$moduleAlias) {
            return reset($modules);
        }

        if (!isset($modules[$moduleAlias])) {
            Logger::getInstance(__CLASS__)->exception(
                ['Module alias {$0} not found in module config files', $moduleAlias],
                __FILE__,
                __LINE__
            );
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
     * @since   0.0
     */
    public static function getAll()
    {
        if (self::$modules === null) {
            self::$modules = [];

            Module::loadConfig('', '', self::$modules, MODULE_CONFIG_PATH);
        }

        return self::$modules;
    }

    /**
     * Load module configs
     *
     * @param $vendor
     * @param string $context
     * @param array $modules
     *
     * @param  string $configFilePath
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     */
    private static function loadConfig(
        $vendor,
        $context,
        array &$modules = [],
        $configFilePath = 'Config/Ice/Core/Module.php'
    )
    {
        $modulePath = $vendor
            ? VENDOR_DIR . $vendor . '/'
            : MODULE_DIR;

        $configPath = $modulePath . $configFilePath;

        $moduleConfig = File::loadData($configPath, false);

        $module = $moduleConfig['module'];

        $module['path'] = $modulePath;

        $moduleDirs = [
            MODULE::CONFIG_DIR,
            MODULE::SOURCE_DIR,
            MODULE::RESOURCE_DIR,
        ];

        if ($modulePath == MODULE_DIR) {
            $moduleDirs = array_merge(
                $moduleDirs,
                [
                    MODULE::LOG_DIR,
                    MODULE::CACHE_DIR,
                    MODULE::UPLOAD_DIR,
                    MODULE::DATA_DIR,
                    MODULE::TEMP_DIR,
                    MODULE::DOWNLOAD_DIR,
                    MODULE::COMPILED_RESOURCE_DIR
                ]
            );
        }

        foreach ($moduleDirs as $dir) {
            $module[$dir] = Directory::get($modulePath . $module[$dir]);
        }

        $module['context'] = $context;

        if (isset($modules[$moduleConfig['alias']])) {
            unset($modules[$moduleConfig['alias']]);
            $modules[$moduleConfig['alias']] = Module::create($moduleConfig['alias'], $module);
            return;
        }

        $modules[$moduleConfig['alias']] = Module::create($moduleConfig['alias'], $module);

        foreach ($moduleConfig['modules'] as $vendor => $context) {
            Module::loadConfig($vendor, $context, $modules);
        }
    }

    public function getDataSourceTables()
    {
        $tables = [];

        foreach ($this->getDataSourceKeys() as $dataSourceKey) {
            $tables[$dataSourceKey] = DataSource::getInstance($dataSourceKey)->getTables($this);
        }

        return $tables;
    }

    public function getDataSourceKeys()
    {
        return array_keys($this->getDataSources());
    }

    public function getDefaultDataSourceKeys()
    {
        if (Module::$defaultDataSourceKeys !== null) {
            return Module::$defaultDataSourceKeys;
        }

        Module::$defaultDataSourceKeys = [];

        foreach ($this->getDataSourceKeys() as $dataSourceKey) {
            $dataSourceName = strstr($dataSourceKey, '/', true);

            if (!isset(Module::$defaultDataSourceKeys[$dataSourceName])) {
                Module::$defaultDataSourceKeys[$dataSourceName] = $dataSourceKey;
            }
        }

        return Module::$defaultDataSourceKeys;
    }

    public static function modulesClear()
    {
        Module::$modules = null;
    }
}
