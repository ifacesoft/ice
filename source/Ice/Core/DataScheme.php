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
use Ice\Helper\Php;
use Ice\Helper\Type_String;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * Class DataScheme
 *
 * Core data scheme container class
 *
 * @see \Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Core
 */
class DataScheme
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
     * @return DataScheme
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.0
     */
    public static function create($dataSourceKey)
    {
        return new DataScheme($dataSourceKey);
    }

    /**
     * Return tables
     *
     * @param  Module $module1
     * @return array
     * @throws \Ice\Exception\Config_Error
     * @throws Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.5
     */
    public static function getTables(Module $module1)
    {
        if (self::$tables !== null) {
            return self::$tables;
        }

        self::$tables = [];
//
//        $moduleDefaultDataSourceKeys = Module::getInstance()->getDefaultDataSourceKeys();

        foreach (Module::getAll() as $module) {
            $sourceDir = $module->getPath(Module::SOURCE_DIR);
            $pattern = '/^' . preg_quote($sourceDir, '/') . '([^\\/]+|[A-Za-z]+\/[A-Za-z]+)' . preg_quote('/Model/', '/') . '.+\.php$/i';
            $Directory = new RecursiveDirectoryIterator($sourceDir);
            $Iterator = new RecursiveIteratorIterator($Directory);
            $Regex = new RegexIterator($Iterator, $pattern, RecursiveRegexIterator::GET_MATCH);

            foreach ($Regex as $filePathes) {
                $modelPath = reset($filePathes);

                $classNames = Php::getClassNamesFromFile($modelPath); // this line fix E_NOTICE: Only variables should be passed by reference
                $modelName = reset($classNames);

                if (Type_String::startsWith($modelName, 'Model_')) {
                    continue;
                }

                /** @var Model $modelClass */
                $modelClass = str_replace(
                        '/',
                        '\\',
                        substr($modelPath, strlen($sourceDir), -4 - strlen($modelName))
                    ) . $modelName;

                $scheme = $modelClass::getConfig()->gets();

                $scheme['modelClass'] = $modelClass;
                $scheme['moduleAlias'] = $module->getAlias();

                if (!isset($scheme['dataSourceKey'])) {
                    continue;
                }

                $dataSourceKey = (array)$scheme['dataSourceKey'];
                $scheme['dataSourceKey'] = reset($dataSourceKey);

                $dataSourceKey = $scheme['dataSourceKey'];

                $tableName = $scheme['scheme']['tableName'];

//                if ($module->getName() != $module1->getName()) {
//                    $dataSourceName = strstr($dataSourceKey, '/', true);
//
//                    if (isset($moduleDefaultDataSourceKeys[$dataSourceName])) {
//                        $dataSourceKey = $moduleDefaultDataSourceKeys[$dataSourceName];
//                    }
//                }

                self::$tables[$dataSourceKey][$tableName] = $scheme;
            }

            unset($filePathes);
        }
//        die();
        return self::$tables;
    }
}
