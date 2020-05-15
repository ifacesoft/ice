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
    'namespace' => 'Ice',
    'module' => [
        'vendor' => 'ifacesoft',
        'name' => 'ice',
        'version' => '1.10.*',
        'description' => 'Ice Open Source PHP Framework',
        'url' => 'http://iceframework.net',
        'type' => 'module',
        'authors' => 'dp <denis.a.shestakov@gmail.com>',
        'vcs' => 'git',
        'source' => 'https://github.com/ifacesoft/Ice.git',
        'Ice\Core\DataSource' => [
            'Ice\DataSource\Mysqli/default.moex' => 'ice_',
        ],
        'pathes' => [
            'configDir' => 'config/',
            'sourceDir' => 'source/',
            'resourceDir' => 'resource/',
            'varDir' => 'var/',
            'logDir' => 'var/log/',
            'cacheDir' => 'var/cache/',
            'uploadDir' => 'var/upload/',
            'dataDir' => 'var/data/',
            'tempDir' => 'var/temp/',
            'backupDir' => 'var/backup/',
            'runDir' => 'var/run/',
            'privateDownloadDir' => 'var/download/',
            'publicDir' => 'public/',
            'compiledResourceDir' => 'public/resource/',
            'downloadDir' => 'public/download/',
        ],
        'ignorePatterns' => [],
        'routerClass' => 'Ice\Router\Ice'
    ],
    'modules' => []
];