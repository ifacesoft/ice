<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 22.01.14
 * Time: 1:44
 */

namespace ice\core;


final class Model_Repository
{
    const DATA_PROVIDER_KEY = 'Buffer:model_repository/';

    private $_dataProvider = null;

    /**
     * @return Model_Repository
     */
    public static function getInstance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new Model_Repository();
        }
        return $inst;
    }

    private function __construct()
    {
        $this->_dataProvider = Data_Provider::getInstance(Model_Repository::DATA_PROVIDER_KEY);
    }

    public static function get($scheme, $pk)
    {
        $dataProvider = self::getInstance()->_dataProvider;
        $dataProvider->setScheme($scheme);
        return $dataProvider->get($pk);
    }

    public static function set($scheme, $pk, $model)
    {
        $dataProvider = self::getInstance()->_dataProvider;
        $dataProvider->setScheme($scheme);
        return $dataProvider->set($pk, $model);
    }
}