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
        'Ice\Core\MessageTransport' => [
            'Ice\MessageTransport\PHPMailer' => [
                'default' => [
                    'debug' => 0,
                    'smtpHost' => '127.0.0.1',
                    'smtpPort' => 25,
                    'smtpUser' => null,
                    'smtpPass' => null,
//            'fromAddress' => 'ice@iceframework.net',
//            'fromName' => 'Robot :)',
//            'replyToAddress' => 'reply@iceframework.net',
//            'replyToName' => 'Test',
                    'redirectTo' => [
                        '/example\.com$/' => []
                    ],
                    'devAddress' => []
                ]
            ]
        ],
        'Ice\Core\DataProvider' => [
            'Ice\DataProvider\Apc' => [
                'default' => [],
            ],
            'Ice\DataProvider\Redis' => [
                'default' => [
                    'host' => '127.0.0.1',
                    'port' => 6379,
                    'timeout' => 1
                ],
            ],
            'Ice\DataProvider\Tarantool' => [
                'localhost' => [
                    'host' => '127.0.0.1',
                    'port' => 3301,
                ],
            ],
            'Ice\DataProvider\File' => [
                'default' => [
                    'path' => 'var/db/'
                ],
            ],
            'Ice\DataProvider\Mysqli' => [
                'default' => [
                    'host' => '127.0.0.1',
                    'username' => 'root',
                    'password' => '',
                    'port' => '3306',
                    'charset' => 'utf8',
                ]
            ],
            'Ice\DataProvider\Mongodb' => [
                'default' => [
                    'host' => 'localhost',
                    'port' => '27017'
                ]
            ],
            'Ice\DataProvider\Cacher' => [
                'default' => [
                    'tagProviders' => []
                ]
            ],
            'Ice\DataProvider\Repository' => [
                'default' => []
            ],
            'Ice\DataProvider\Session' => [
                'default' => []
            ],
            'Ice\DataProvider\Security' => [
                'default' => []
            ],
            'Ice\DataProvider\Cli' => [
                'default' => []
            ],
            'Ice\DataProvider\Router' => [
                'default' => []
            ],
            'Ice\DataProvider\Request_Http_Raw' => [
                'default' => []
            ],
            'Ice\DataProvider\QueryResultRow' => [
                'default' => []
            ],
        ],
        'ini_set_session' => [
            'use_cookies' => 1,
            'cookie_domain' => '',
            'cookie_lifetime' => 86400,
            'gc_maxlifetime' => 86400,
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
            'Ice\Core\DataScheme' => [
                'data_scheme' => 'Ice:Repository',
            ],
            'Ice\Core\ModelScheme' => [
                'model_scheme' => 'Ice:Repository',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:Repository',
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
            'Ice\Core\DataScheme' => [
                'data_scheme' => 'Ice:File',
            ],
            'Ice\Core\ModelScheme' => [
                'model_scheme' => 'Ice:File',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:File',
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
            'Ice\Core\DataScheme' => [
                'data_scheme' => 'Ice:Registry',
            ],
            'Ice\Core\ModelScheme' => [
                'model_scheme' => 'Ice:Registry',
            ],
            'Ice\Core\Cache' => [
                'tags' => 'Ice:File',
            ],
            'Ice\Core\Session' => [
                'session' => 'Ice:File'
            ],
        ],
    ]
];