<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 08.01.14
 * Time: 20:11
 */

namespace ice\core;

use ice\Ice;

class Model_Scheme
{
    /** @var Config */
    private $_modelSchemeConfig = null;

    private function __construct(Config $modelSchemeConfig)
    {
        $this->_modelSchemeConfig = $modelSchemeConfig;
    }

    public static function get($modelClass)
    {
        $dataProvider = Data_Provider::getInstance(
            Ice::getConfig()->getParam('modelSchemeDataProviderKey') . $modelClass
        );

        $modelScheme = $dataProvider->get($modelClass);

        if ($modelScheme) {
            return $modelScheme;
        }

        $modelSchemeConfig = Config::get($modelClass, array(), 'Scheme');

        $modelScheme = $modelSchemeConfig
            ? new Model_Scheme($modelSchemeConfig)
            : Model_Scheme::create($modelClass);

        if ($modelScheme) {
            $dataProvider->set($modelClass, $modelScheme);
        }

        return $modelScheme;
    }

    private static function create($modelClass)
    {
        $dataMapping = Data_Mapping::get();
        $dataMapping->add($modelClass);

        $tableName = Data_Mapping::get()->getModelClasses()[$modelClass];
        $modelSchemeConfigData = Data_Source::getDefault()->getDataScheme()[$tableName];

        $modelSchemeConfig = Config::create($modelClass, $modelSchemeConfigData, 'Scheme');

        return new Model_Scheme($modelSchemeConfig);

    }

    public function getColumns()
    {
        return $this->_modelSchemeConfig->getParams();
    }

    public function getColumnNames()
    {
        return array_keys($this->getColumns());
    }
}