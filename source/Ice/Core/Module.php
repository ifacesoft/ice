<?php
/**
 * Ice core module class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Codeception\Util\Debug;
use Ice\Exception\Config_Error;
use Ice\Exception\Error;
use Ice\Exception\FileNotFound;
use Ice\Exception\ModuleNotFound;
use Ice\Helper\Directory;
use Ice\Helper\File;
use Ice\Helper\Type_String;

/**
 * Class Module
 *
 * Core module class
 *
 * @see \Ice\Core\Container
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
    const VAR_DIR = 'varDir';
    const LOG_DIR = 'logDir';
    const CACHE_DIR = 'cacheDir';
    const UPLOAD_DIR = 'uploadDir';
    const DATA_DIR = 'dataDir';
    const TEMP_DIR = 'tempDir';
    const BACKUP_DIR = 'backupDir';
    const RUN_DIR = 'runDir';
    const PRIVATE_DOWNLOAD_DIR = 'privateDownloadDir';
    const PUBLIC_DIR = 'publicDir';
    const COMPILED_RESOURCE_DIR = 'compiledResourceDir';
    const DOWNLOAD_DIR = 'downloadDir';

    const DIR = 'path'; // где-то еще используется не как константа
//
//    public static $loaded = false;

    public static $defaultConfig = [
        'alias' => 'Draft',
        'namespace' => 'Draft',
        'module' => [
            'pathes' => [
                Module::CONFIG_DIR => 'config/',
                Module::SOURCE_DIR => 'source/',
                Module::RESOURCE_DIR => 'resource/',
                Module::VAR_DIR => 'var',
                Module::LOG_DIR => 'var/log/',
                Module::CACHE_DIR => 'var/cache/',
                Module::UPLOAD_DIR => 'var/upload/',
                Module::DATA_DIR => 'var/data/',
                Module::TEMP_DIR => 'var/temp/',
                Module::BACKUP_DIR => 'var/backup/',
                Module::RUN_DIR => 'var/run/',
                Module::PRIVATE_DOWNLOAD_DIR => 'var/download/',
                Module::PUBLIC_DIR => 'public/',
                Module::COMPILED_RESOURCE_DIR => 'public/resource/',
                Module::DOWNLOAD_DIR => 'public/download/',
            ],
            'ignorePatterns' => [],
            'routerClass' => 'Ice\Router\Ice'
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

    public static function reload()
    {
        Module::init();
    }

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
     * @throws Exception
     */
    public function getModuleAliasByTableName($tableName, $dataSourceKey)
    {
        foreach ($this->getDataSourcePrefixes($dataSourceKey) as $moduleAlias => $prefixes) {
            foreach ($prefixes as $prefix) {
                if ($prefix === '' || Type_String::startsWith($tableName, $prefix)) {
                    return $moduleAlias;
                }
            }
        }

        return null;
    }

    /**
     * Return table prefixes for module
     *
     * @param  $dataSourceKey
     * @param null $module
     * @return array
     * @throws Config_Error
     * @throws FileNotFound
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.10
     * @since   0.5
     */
    public function getDataSourcePrefixes($dataSourceKey, $module = null)
    {
        $prefixes = [];

        $modules = $module
            ? [$module]
            : Module::getAll();

        foreach ($modules as $module) {
            $moduleAlias = $module->getAlias();

            foreach ($module->gets(DataSource::class) as $key => $tablePrefixes) {
                if ($key != $dataSourceKey) {
                    continue;
                }

                if (!isset($prefixes[$moduleAlias])) {
                    $prefixes[$moduleAlias] = [];
                }

                $prefixes[$moduleAlias] += (array)$tablePrefixes;
            }
            unset($tablePrefixes);
        }

        return $prefixes;
    }

    /**
     * @throws Exception
     */
    public static function init()
    {
        self::$modules = is_file(MODULE_DIR . MODULE_CONFIG_PATH)
            ? self::loadConfig('', '', [], MODULE_CONFIG_PATH)
            : self::loadConfig(ICE_VENDOR_NAME, '', [], ICE_CONFIG_PATH);

        if (!defined('MODULE_NAME')) {
            reset(self::$modules);
            define('MODULE_NAME', key(self::$modules));
        }

        require_once ICE_DIR . 'source/helper.php';
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
            if (file_exists(MODULE_DIR . 'vendor/ifacesoft/ice/source/bootstrap.php')) {
                require_once MODULE_DIR . 'vendor/ifacesoft/ice/source/bootstrap.php';
            } else {
                require_once MODULE_DIR . 'source/bootstrap.php';
            }
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
     * @param string $configFilePath
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.14
     * @since   0.0
     *
     * @return array
     * @throws Exception
     */
    private static function loadConfig($vendor, $context, array $modules, $configFilePath = ICE_CONFIG_PATH)
    {
        $modulePath = $vendor
            ? VENDOR_DIR . $vendor . '/'
            : MODULE_DIR;

        $configPath = $modulePath . $configFilePath;

        $moduleConfig = File::loadData($configPath);

        $module = $moduleConfig['module'];

        $module['namespace'] = $moduleConfig['namespace'];

        $module[Module::DIR] = $modulePath;

        $moduleDirs = [
            Module::CONFIG_DIR,
            Module::SOURCE_DIR,
            Module::RESOURCE_DIR,
        ];

//        if ($modulePath == MODULE_DIR) {
            $moduleDirs = array_merge(
                $moduleDirs,
                [
                    Module::VAR_DIR,
                    Module::LOG_DIR,
                    Module::CACHE_DIR,
                    Module::UPLOAD_DIR,
                    Module::DATA_DIR,
                    Module::TEMP_DIR,
                    Module::BACKUP_DIR,
                    Module::RUN_DIR,
                    Module::PUBLIC_DIR,
                    Module::DOWNLOAD_DIR,
                    Module::PRIVATE_DOWNLOAD_DIR,
                    Module::COMPILED_RESOURCE_DIR
                ]
            );
//        }

        foreach ($moduleDirs as $dir) {
            $module['pathes'][$dir] = Directory::get($modulePath . $module['pathes'][$dir]);
        }

        $module['context'] = $context;

        if (isset($modules[$moduleConfig['alias']])) {
            unset($modules[$moduleConfig['alias']]);
            $modules[$moduleConfig['alias']] = self::create($moduleConfig['alias'], $module);
            return $modules;
        }

        $modules[$moduleConfig['alias']] = self::create($moduleConfig['alias'], $module);

        foreach ($moduleConfig['modules'] as $vendor => $context) {
            $modules = self::loadConfig($vendor, $context, $modules);
        }

        return $modules;
    }

    public function getAlias()
    {
        return $this->getName();
    }

    public function getNamespace()
    {
        return $this->get('namespace', '');
    }

    /**
     * @param $tableName
     * @param $dataSourceKey
     * @param array $aliasNamespaceMap
     * @return string
     * @throws Exception
     */
    public function getModelClass($tableName, $dataSourceKey, $aliasNamespaceMap = [])
    {
        $namespace = null;
        $tableNamePart = $tableName;

        $tables = DataScheme::getTables($this);

        if (isset($tables[$dataSourceKey][$tableName]['modelClass'])) {
            return $tables[$dataSourceKey][$tableName]['modelClass'];
        }

        foreach ($this->getDataSourcePrefixes($dataSourceKey, $this) as $moduleAlias => $prefixes) {
            foreach ($prefixes as $prefix) {
                if ($prefix === '' || Type_String::startsWith($tableName, $prefix)) {
                    $tableNamePart = substr($tableName, strlen($prefix));
                    $module = Module::getInstance($moduleAlias);
                    $namespace = $module->getNamespace();

                    break 2;
                }
            }
            unset($prefix);
        }

        if (!$namespace) {
            $namespace = Module::getInstance()->getNamespace();
        }

        if (isset($aliasNamespaceMap[$namespace])) {
            $namespace = $aliasNamespaceMap[$namespace];
        }

        $modelName = $namespace . '\Model\\';

        foreach (explode('_', preg_replace('/_{2,}/', '_', $tableNamePart)) as $modelNamePart) {
            $modelName .= ucfirst($modelNamePart) . '_';
        }

        return rtrim($modelName, '_');
    }

    /**
     * Get module instance by module alias
     *
     * @param  string $moduleAlias
     * @param  null $postfix
     * @param  bool $isRequired
     * @param  null $ttl
     * @param array $selfConfig
     * @return Module
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since   0.0
     * @throws Exception
     */
    public static function getInstance($moduleAlias = null, $postfix = null, $isRequired = false, $ttl = null, array $selfConfig = [])
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

    public function getDataSourceAliases()
    {
        $aliases = [];

        foreach (Module::getAll() as $module) {
            $moduleAlias = $module->getAlias();

            foreach ($module->gets(DataSource::class) as $dataSourceKey => $prefixes) {
                if (!isset($aliases[$dataSourceKey])) {
                    $aliases[$dataSourceKey] = $moduleAlias;
                }
            }
            unset($prefixes);
        }

        return $aliases;
    }

    /**
     * @return array
     * @throws Exception
     */
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
        $dataSourceKeys = array_keys($this->gets(DataSource::class));

        foreach (Module::getAll() as $module) {
            foreach ($module->gets(DataSource::class) as $dataSourceKey => $prefixes) {
                $dataSourceKeys[] = $dataSourceKey;
            }
            unset($prefixes);
        }

        return array_unique($dataSourceKeys); // todo: индексами сделать что-то вроде mysql:default.test
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

    public function getPath($pathParam = null, $isRequired_default = true)
    {
        return $pathParam
            ? $this->get('pathes/' . $pathParam, $isRequired_default)
            : $this->get(Module::DIR);
    }
}
