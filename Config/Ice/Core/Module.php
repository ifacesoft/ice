<?php
/**
 * @file Ice modules config
 *
 * Sets default config params for ice application components
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @version 0.0
 * @since 0.0
 */

return [
    'alias' => 'Ice',
    'module' => [
        'name' => 'Ice',
        'version' => '1.0.6',
        'description' => 'Ice Open Source PHP Framework',
        'url' => 'http://iceframework.net',
        'type' => 'module',
        'authors' => 'dp <denis.a.shestakov@gmail.com>',
        'vcs' => 'git',
        'source' => 'https://github.com/ifacesoft/Ice.git',
        'Ice\Core\Data_Source' => [
            'Ice\Data\Source\Mysqli/default.test' => 'ice_',
            'Ice\Data\Source\Mongodb/default.test' => ''
        ],
        'configDir' => 'Config/',
        'sourceDir' => 'Source/',
        'resourceDir' => 'Resource/',
        'logDir' => 'Var/log/',
        'cacheDir' => 'Var/cache/',
        'uploadDir' => 'Var/upload/',
        'uploadTempDir' => 'Var/upload/temp/',
        'compiledResourceDir' => 'Web/resource/',
        'downloadDir' => 'Web/download/',
        'bootstrapClass' => 'Ice\Bootstrap\Ice',
        'securityClass' => 'Ice\Security\Ice',
        'routerClass' => 'Ice\Router\Ice'
    ],
    'modules' => []
];