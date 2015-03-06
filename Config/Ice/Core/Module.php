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
        'name' => 'Ice Php Framework',
        'type' => 'module',
        'url' => 'http://iceframework.net',
        'authors' => 'dp <denis.a.shestakov@gmail.com>',
        'vcs' => 'git',
        'source' => 'https://github.com/ifacesoft/Ice.git',
        'Ice\Core\Model' => [
            'dataSources' => [
                'Ice\Data\Source\Mysqli/default.test' => ['ice_'],
                'Ice\Data\Source\Mongodb/default.test' => ['ice_'],
            ]
        ],
    ],
    'modules' => []
];