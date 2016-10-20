<?php
/**
 * @file Environment config
 *
 * Sets default config params for ice application components
 *
 * @author dp <denis.a.shestakov@gmail.com>
 */

return [
    'production' => [
        'Ice\Core\DataProvider' => [
            'Ice\DataProvider\Redis' => [
                'session' => [
                    'ttl' => 60 * 60 * 24
                ],
            ],
            'Ice\DataProvider\File' => [
                'session' => [
                    'ttl' => 60 * 60 * 24
                ],
            ],
            'Ice\DataProvider\Mysqli' => [
                'default' => [
                    'username' => 'root',
                    'password' => '',
                ]
            ],
            'Ice\DataProvider\Mongodb' => [
                'default' => [
                ]
            ]
        ],
        'dataProviderKeys' => [
            'Ice\Core\Loader' => [
                'loader' => 'Ice:Registry',
            ],
            'Ice\Core\Action' => [
                'cache' => 'Ice:Repository'
            ],
            'Ice\Core\Route' => [
                'route' => 'Ice:Repository',
                'routes' => 'Ice:Repository',
            ],
            'Ice\Core\Config' => [
                'config' => 'Ice:Repository',
                'route' => 'Ice:Repository',
                'routes' => 'Ice:Repository',
                'model_scheme' => 'Ice:Repository',
            ],
            'Ice\Core\Query' => [
                'sql' => 'Ice:Repository',
                'query' => 'Ice:Repository',
            ],
            'Ice\Core\DataSource' => [
                'cache' => 'Ice:File'
            ],
            'Ice\Core\Data_Scheme' => [
                'data_scheme' => 'Ice:Repository',
            ],
            'Ice\Core\ModelScheme' => [
                'model_scheme' => 'Ice:Repository',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:Repository',
            ],
            'Ice\Core\ViiewOld' => [
                'view' => 'Ice:Repository',
            ],
            'Ice\Core\Session' => [
                'session' => 'Ice:File'
            ],
        ],
    ],
    'test' => [
        'dataProviderKeys' => [
            'Ice\Core\Loader' => [
                'loader' => 'Ice:Registry',
            ],
            'Ice\Core\Action' => [
                'cache' => 'Ice:File'
            ],
            'Ice\Core\Route' => [
                'route' => 'Ice:File',
                'routes' => 'Ice:File',
            ],
            'Ice\Core\Config' => [
                'config' => 'Ice:File',
                'route' => 'Ice:File',
                'routes' => 'Ice:File',
                'model_scheme' => 'Ice:File',
            ],
            'Ice\Core\Query' => [
                'sql' => 'Ice:File',
                'query' => 'Ice:File',
            ],
            'Ice\Core\DataSource' => [
                'cache' => 'Ice:File'
            ],
            'Ice\Core\Data_Scheme' => [
                'data_scheme' => 'Ice:File',
            ],
            'Ice\Core\ModelScheme' => [
                'model_scheme' => 'Ice:File',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:File',
            ],
            'Ice\Core\ViiewOld' => [
                'view' => 'Ice:File',
            ],
            'Ice\Core\Session' => [
                'session' => 'Ice:File'
            ],
        ]
    ],
    'development' => [
        'dataProviderKeys' => [
            'Ice\Core\Loader' => [
                'loader' => 'Ice:Registry',
            ],
            'Ice\Core\Action' => [
                'output' => 'Ice:Registry'
            ],
            'Ice\Core\Route' => [
                'route' => 'Ice:Registry',
                'routes' => 'Ice:Registry',
            ],
            'Ice\Core\Config' => [
                'config' => 'Ice:Registry',
                'route' => 'Ice:Registry',
                'routes' => 'Ice:Registry',
                'model_scheme' => 'Ice:Registry',
            ],
            'Ice\Core\Query' => [
                'sql' => 'Ice:Registry',
                'query' => 'Ice:Registry',
            ],
            'Ice\Core\DataSource' => [
                'cache' => 'Ice:Registry'
            ],
            'Ice\Core\Data_Scheme' => [
                'data_scheme' => 'Ice:Registry',
            ],
            'Ice\Core\ModelScheme' => [
                'model_scheme' => 'Ice:Registry',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:File',
            ],
            'Ice\Core\ViiewOld' => [
                'view' => 'Ice:Registry',
            ],
            'Ice\Core\Session' => [
                'session' => 'Ice:File'
            ],
        ],
    ]
];