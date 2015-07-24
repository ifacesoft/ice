<?php
/**
 * Ice core data scheme container class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Directory;
use Ice\Helper\Json;
use Ice\Helper\Php;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * Class Data_Scheme
 *
 * Core data scheme container class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class Data_Scheme
{
    use Core;

    private static $tables = null;
    /**
     * Data source key
     *
     * @var string
     */
    private $dataSourceKey = null;

    /**
     * Private constructor of dat scheme
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     * @param   $dataSourceKey
     */
    private function __construct($dataSourceKey)
    {
        $this->dataSourceKey = $dataSourceKey;
    }

    /**
     * Create new instance of data scheme
     *
     * @param  $dataSourceKey
     * @return Data_Scheme
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public static function create($dataSourceKey)
    {
        return new Data_Scheme($dataSourceKey);
    }

    /**
     * Return tables
     *
     * @param  Module $module1
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since   0.5
     */
    public static function getTables(Module $module1)
    {
        if (self::$tables !== null) {
            return self::$tables;
        }

        self::$tables = [];

        $moduleDefaultDataSourceKeys = Module::getInstance()->getDefaultDataSourceKeys();

        foreach(Module::getAll() as $module) {
            $sourceDir = $module->get(Module::SOURCE_DIR);

            $Directory = new RecursiveDirectoryIterator(Directory::get($sourceDir . $module->getAlias() . '/Model'));
            $Iterator = new RecursiveIteratorIterator($Directory);
            $Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

            foreach ($Regex as $filePathes) {
                $modelPath = reset($filePathes);
                $classNames = Php::getClassNamesFromFile($modelPath);
                $modelName = reset($classNames);

                $modelClass = str_replace(
                        '/',
                        '\\',
                        substr($modelPath, strlen($sourceDir), -4 - strlen($modelName))
                    ) . $modelName;

                $config = $modelClass::getConfig()->gets();

                $config['modelClass'] = $modelClass;
                $config['modelPath'] = substr($modelPath, strlen($sourceDir));

                $dataSourceKey = $config['dataSourceKey'];

                if ($module->getName() != $module1->getName()) {
                    $dataSourceName = strstr($dataSourceKey, '/', true);

                    if (isset($moduleDefaultDataSourceKeys[$dataSourceName])) {
                        $dataSourceKey = $moduleDefaultDataSourceKeys[$dataSourceName];
                    }
                }

                self::$tables[$dataSourceKey][$config['scheme']['tableName']] = $config;
            }
        }

        return self::$tables;
    }
}
