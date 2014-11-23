<?php
/**
 * Ice core data scheme container class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Core\Model;
use Ice\Exception\File_Not_Found;
use Ice\Helper\Arrays;
use Ice\Helper\Date;
use Ice\Helper\File;

/**
 * Class Data_Scheme
 *
 * Core data scheme container class
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
class Data_Scheme extends Container
{
    use Core;

    /**
     * Data scheme data
     *
     * @var array
     */
    private $_dataScheme = null;

    /**
     * Map of model classes and their table names
     *
     * @var array
     */
    private $_modelClasses = null;

    /**
     * Private constructor of dat scheme
     *
     * @param $dataScheme
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct($dataScheme)
    {
        $this->_dataScheme = $dataScheme;
    }

    /**
     * Create new instance of data scheme
     *
     * @param $scheme
     * @param null $hash
     * @return Data_Scheme
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($scheme, $hash = null)
    {
        return new Data_Scheme(self::getFilePathData($scheme));
    }

    /**
     * Return path to data scheme data
     *
     * @param $scheme
     * @return mixed
     * @throws File_Not_Found
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getFilePathData($scheme)
    {
        $filePath = Loader::getFilePath($scheme, '.php', 'Var/Scheme/', false, true);

        if (file_exists($filePath)) {
            return File::loadData($filePath);
        }

        $data = [
            'time' => Date::get(),
            'revision' => date('00000000'),
            'tables' => []
        ];

        File::createData($filePath, $data);

        return self::update($scheme);
    }

    /**
     * Synchronization local data scheme with remote data source scheme
     *
     * @param $scheme
     * @param bool $force
     * @return mixed
     * @throws File_Not_Found
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function update($scheme, $force = false)
    {
        $schemeData = [
            'time' => Date::get(),
            'revision' => date('mdHi'),
            'scheme' => $scheme
        ];

        $localTables = Data_Scheme::getFilePathData($scheme)['tables'];
        $sourceTables = Data_Source::getInstance($scheme)->getTables();

        $diff = Data_Scheme::diff($localTables, $sourceTables);

        $tables = [
            'updated' => [],
            'notChanged' => []
        ];

        foreach ($diff['added'] as $tableName => $table) {
            $modelScheme = Model_Scheme::update($tableName, $schemeData, $force);

            if (empty($table['engine'])) {
                continue;
            }

            $table['modelClass'] = $modelScheme['modelClass'];
            $table['revision'] = $modelScheme['revision'];
            $tables['updated'][$tableName] = $table;
        }

        foreach ($diff['other'] as $tableName => $table) {
            $modelSchemeDiff = Model_Scheme::diff($scheme, $tableName);

            if (empty($modelSchemeDiff['added']) && empty($modelSchemeDiff['deleted'])) {
                $tables['notChanged'][$tableName] = $localTables[$tableName];
                continue;
            }

            $modelScheme = Model_Scheme::update($tableName, $schemeData, $force);
            $table['modelClass'] = $modelScheme['modelClass'];
            $table['revision'] = $modelScheme['revision'];
            $tables['updated'][$tableName] = $table;
        }

        if (empty($diff['deleted']) && empty($tables['updated']) && !$force) {
            return self::getFilePathData($scheme);
        }

        $schemeData['tables'] = array_merge($tables['updated'], $tables['notChanged']);
        unset($tables);

        ksort($schemeData['tables']);

        $dataSchemeFile = Loader::getFilePath($scheme, '.php', 'Var/Scheme/', false, true);

        $prevRevision = self::getFilePathData($scheme)['revision'];
        $prevDataSchemeFile = Loader::getFilePath($scheme . '/' . $prevRevision, '.php', 'Var/Scheme/', false, true);

        File::move($dataSchemeFile, $prevDataSchemeFile);

        Data_Scheme::getLogger()->info(['Update scheme for tables: {$0}', Ice\Helper\Php::varToPhpString(array_keys($schemeData['tables']))], Logger::SUCCESS, true);

        return File::createData($dataSchemeFile, $schemeData);
    }

    /**
     * Return different between local data scheme with remote data source scheme
     *
     * @param array $localTables
     * @param array $sourceTables
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function diff(array $localTables, array $sourceTables)
    {
        return Arrays::diff($localTables, $sourceTables);
    }

    /**
     * Return default scheme name
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function getDefaultKey()
    {
        $schemes = array_keys(Data_Source::getConfig()->gets());
        return reset($schemes);
    }

    /**
     * Return map tables short scheme
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getTableNames()
    {
        return $this->_dataScheme['tables'];
    }

    /**
     * Return map of model classes and their table names
     *
     * @return Model[]
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getModelClasses()
    {
        if ($this->_modelClasses !== null) {
            return $this->_modelClasses;
        }

        return $this->_modelClasses = array_flip(Arrays::column($this->getTableNames(), 'modelClass'));
    }

    /**
     * Return current scheme name
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getName()
    {
        return $this->_dataScheme['scheme'];
    }
}