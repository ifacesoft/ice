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
        'version' => '1.0.*',
        'description' => 'Ice Open Source PHP Framework',
        'url' => 'http://iceframework.net',
        'type' => 'module',
        'authors' => 'dp <denis.a.shestakov@gmail.com>',
        'vcs' => 'git',
        'source' => 'https://github.com/ifacesoft/Ice.git',
        'Ice\Core\DataSource' => [
            'Ice\DataSource\Mysqli/default.test' => 'ice_',
            'Ice\DataSource\Mongodb/default.test' => ''
        ],
        'configDir' => 'Config/',
        'sourceDir' => 'Source/',
        'resourceDir' => 'Resource/',
        'logDir' => 'Var/log/',
        'cacheDir' => 'Var/cache/',
        'uploadDir' => 'Var/upload/',
        'tempDir' => 'Var/temp/',
        'compiledResourceDir' => 'Web/resource/',
        'downloadDir' => 'Web/download/',
        'privateDownloadDir' => 'Var/download/',
        'ignorePatterns' => [],
        'bootstrapClass' => 'Ice\Bootstrap\Ice',
        'routerClass' => 'Ice\Router\Ice'
    ],
    'modules' => []
];