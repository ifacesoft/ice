<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 10.01.14
 * Time: 9:38
 */

namespace ice\core;

use ice\Ice;

class Data_Mapping
{
    const DATA_PROVIDER_KEY = 'dataMappingDataProviderKey';
    const CONFIG_TABLES = 'tables';
    const CONFIG_PREFIXES = 'prefixes';

    /** @var Config */
    private $_dataMappingConfig = null;

    private function __construct(Config $dataMappingConfig)
    {
        $this->_dataMappingConfig = $dataMappingConfig;
    }

    public static function add($modelClass)
    {
        $dataMappingConfigData = Data_Mapping::get()->getModelClasses();
        $dataMappingConfigData[$modelClass] = \ice\core\helper\Data_Mapping::getTableNameByClass($modelClass);

        $dataMapping = new Data_Mapping(Config::create(__CLASS__, $dataMappingConfigData));


        if ($dataMapping) {
            Data_Provider::getInstance(Ice::getConfig()->getParam(self::DATA_PROVIDER_KEY) . __CLASS__)
                ->set(__CLASS__, $dataMapping);
        }
    }

    public function getModelClasses()
    {
        return (array)$this->_dataMappingConfig->getParams(self::CONFIG_TABLES, false);
    }

    public function getPrefixes()
    {
        return (array)$this->_dataMappingConfig->getParams(self::CONFIG_PREFIXES, false);
    }

    public static function get()
    {
        $dataProvider = Data_Provider::getInstance(Ice::getConfig()->getParam(self::DATA_PROVIDER_KEY) . __CLASS__);

        $dataMapping = $dataProvider->get(__CLASS__);

        if ($dataMapping) {
            return $dataMapping;
        }

        $dataMappingConfig = Config::get(__CLASS__, array());

        $dataMapping = $dataMappingConfig
            ? new Data_Mapping($dataMappingConfig)
            : Data_Mapping::create();

        if ($dataMapping) {
            $dataProvider->set(__CLASS__, $dataMapping);
        }

        return $dataMapping;
    }

    private static function create()
    {
        return new Data_Mapping(Config::create(__CLASS__, array()));
    }

    public static function getClass()
    {
        return get_called_class();
    }
}