<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 25.12.13
 * Time: 0:27
 */

define('OBJECT_CACHE', function_exists('apc_store') ? 'Apc' : 'Buffer');
define('STRING_CACHE', class_exists('Redis') ? 'Redis' : 'File');

return array(
    'defaultDataSourceKey' => 'Mysqli:default/mysql',
    'defaultViewRenderClass' => 'ice\view\render\Php',
    'loaderDataProviderKey' => OBJECT_CACHE . ':loader/',
    'configDataProviderKey' => OBJECT_CACHE . ':config/',
    'viewRenderDataProviderKey' => OBJECT_CACHE . ':view_render/',
    'queryTranslatorDataProviderKey' => OBJECT_CACHE . ':query_translator/',
    'modelSchemeDataProviderKey' => OBJECT_CACHE . ':model_scheme/',
    'modelMappingDataProviderKey' => OBJECT_CACHE . ':model_mapping/',
    'dataMappingDataProviderKey' => OBJECT_CACHE . ':data_mapping/',
    'routerDataProviderKey' => OBJECT_CACHE . ':router/',
    'actionDataProviderKey' => OBJECT_CACHE . ':action/',
    'modules' => array(
        'Ice' => array(
            'path' => dirname(__DIR__) . '/Ice/',
            'productionHost' => 'ice.ifacesoft.ru'
        )
    ),
    'dataProviders' => array(
        'Defined:model' => array(),
        'Factory:model' => array(),
        OBJECT_CACHE . ':loader' => array(),
        OBJECT_CACHE . ':config' => array(),
        OBJECT_CACHE . ':view_render' => array(),
        OBJECT_CACHE . ':query_translator' => array(),
        OBJECT_CACHE . ':model_scheme' => array(),
        OBJECT_CACHE . ':model_mapping' => array(),
        OBJECT_CACHE . ':data_mapping' => array(),
        OBJECT_CACHE . ':router' => array(),
        OBJECT_CACHE . ':action' => array(),
        'Request:http' => array(),
        'Cli:prompt' => array(),
        'Session:php' => array(),
        'Buffer:view_render' => array(),
        'Buffer:model_repository' => array(),
        'Buffer:action' => array(),
        STRING_CACHE . ':default' => array(
            'host' => 'localhost',
            'port' => 6379
        ),
        'Router:default' => array(),
        'File:tmp' => array(
            'path' => sys_get_temp_dir() . '/'
        ),
        'Mysqli:ice.default' => array(
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8'
        )
    )
);